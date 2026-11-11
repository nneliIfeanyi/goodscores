/**
 * Local development uses the XAMPP folder path. Production uses the
 * same-origin backend path so the app can be served from the domain root.
 *
 * Change FOLDER below if your htdocs folder name is different.
 */
export const FOLDER = 'goodscores';
export const API_BASE = globalThis.GOODSCORES_API_BASE
  || (['localhost', '127.0.0.1'].includes(window.location.hostname)
    ? `http://localhost/${FOLDER}/backend/public`
    : '');

let activeWriteRequests = 0;
let networkLoader;

function getNetworkLoader() {
  if (networkLoader) return networkLoader;
  networkLoader = document.createElement('div');
  networkLoader.id = 'network-loader';
  networkLoader.setAttribute('role', 'status');
  networkLoader.setAttribute('aria-live', 'polite');
  networkLoader.innerHTML = '<span class="network-loader-spinner" aria-hidden="true"></span><span data-network-loader-label>Working…</span>';
  document.body.appendChild(networkLoader);
  return networkLoader;
}

function startNetworkLoader(endpoint) {
  activeWriteRequests += 1;
  const loader = getNetworkLoader();
  const label = loader.querySelector('[data-network-loader-label]');
  label.textContent = endpoint.includes('/ocr') || endpoint.includes('/ai/')
    ? 'Working with AI…'
    : 'Processing…';
  loader.classList.add('is-visible');
}

function stopNetworkLoader() {
  activeWriteRequests = Math.max(0, activeWriteRequests - 1);
  if (activeWriteRequests === 0) getNetworkLoader().classList.remove('is-visible');
}

function getToken() {
  return localStorage.getItem('gs_token');
}

export async function api(endpoint, options = {}) {
  const method = (options.method || 'GET').toUpperCase();
  const showNetworkLoader = method !== 'GET';
  if (showNetworkLoader) startNetworkLoader(endpoint);
  const headers = {
    'Content-Type': 'application/json',
    ...(options.headers || {}),
  };

  const token = getToken();
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  let res;
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), options.timeoutMs ?? 10000);
  const requestSignal = options.signal;
  if (requestSignal) {
    requestSignal.addEventListener('abort', () => controller.abort(), { once: true });
  }
  try {
    try {
      res = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers,
        signal: controller.signal,
      });
    } catch (networkErr) {
      const err = new Error(networkErr.name === 'AbortError'
        ? 'The server took too long to respond. Please check your connection and try again.'
        : 'Unable to reach the GoodScores server. Check your connection or server configuration and try again.');
      err.status = 0;
      err.offline = true;
      err.cause = networkErr;
      throw err;
    }

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      const baseMessage = data.error || data.message || 'Request failed';
      const debugMessage = data.debug_message && window.location.hostname === 'localhost'
        ? ` (${data.debug_message})`
        : '';
      const err = new Error(baseMessage + debugMessage);
      err.status = res.status;
      err.data = data;
      throw err;
    }

    return data;
  } finally {
    clearTimeout(timeout);
    if (showNetworkLoader) stopNetworkLoader();
  }
}

export async function apiStream(endpoint, options = {}, onProgress = () => {}) {
  const method = (options.method || 'GET').toUpperCase();
  const showNetworkLoader = method !== 'GET';
  if (showNetworkLoader) startNetworkLoader(endpoint);

  const headers = {
    'Content-Type': 'application/json',
    Accept: 'application/x-ndjson',
    ...(options.headers || {}),
  };
  const token = getToken();
  if (token) {
    headers.Authorization = 'Bearer ' + token;
  }

  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), options.timeoutMs ?? 75000);
  const requestSignal = options.signal;
  const abortRequest = () => controller.abort();
  if (requestSignal?.aborted) {
    abortRequest();
  } else {
    requestSignal?.addEventListener('abort', abortRequest, { once: true });
  }

  try {
    let response;
    try {
      response = await fetch(`${API_BASE}${endpoint}`, {
        ...options,
        headers,
        signal: controller.signal,
      });
    } catch (networkErr) {
      const error = new Error(networkErr.name === 'AbortError'
        ? 'The server took too long to respond. Please check your connection and try again.'
        : 'Unable to reach the GoodScores server. Check your connection or server configuration and try again.');
      error.status = 0;
      error.offline = true;
      error.cause = networkErr;
      throw error;
    }

    if (!response.ok) {
      const data = await response.json().catch(() => ({}));
      const baseMessage = data.error || data.message || 'Request failed';
      const debugMessage = data.debug_message && window.location.hostname === 'localhost'
        ? ` (${data.debug_message})`
        : '';
      const error = new Error(baseMessage + debugMessage);
      error.status = response.status;
      error.data = data;
      throw error;
    }

    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/x-ndjson')) {
      const data = await response.json().catch(() => ({}));
      const error = new Error(data.error || data.message || 'The server did not start an AI progress stream.');
      error.status = response.status;
      error.data = data;
      throw error;
    }

    const reader = response.body && response.body.getReader();
    if (!reader) {
      throw new Error('This browser cannot read the AI progress stream.');
    }

    const decoder = new TextDecoder();
    let buffer = '';
    let completedResponse;
    const handleLine = (line) => {
      if (!line.trim()) return;

      let event;
      try {
        event = JSON.parse(line);
      } catch {
        throw new Error('The server returned an invalid AI progress update. Please try again.');
      }

      if (event.type === 'progress') {
        onProgress(event);
        return;
      }
      if (event.type === 'error') {
        const error = new Error(event.message || 'Could not generate questions');
        error.status = event.status ?? 502;
        error.data = event;
        throw error;
      }
      if (event.type === 'complete') {
        onProgress({ ...event, phase: 'complete' });
        completedResponse = event.response;
        return;
      }
      throw new Error('The server returned an unknown AI progress update.');
    };

    while (true) {
      let chunk;
      try {
        chunk = await reader.read();
      } catch (networkErr) {
        const error = new Error(networkErr.name === 'AbortError'
          ? 'The server took too long to respond. Please check your connection and try again.'
          : 'The AI progress stream was interrupted. Check your connection and try again.');
        error.status = 0;
        error.offline = true;
        error.cause = networkErr;
        throw error;
      }

      buffer += decoder.decode(chunk.value || new Uint8Array(), { stream: !chunk.done });
      let newline;
      while ((newline = buffer.indexOf('\n')) !== -1) {
        handleLine(buffer.slice(0, newline).replace(/\r$/, ''));
        buffer = buffer.slice(newline + 1);
      }
      if (chunk.done) break;
    }

    if (buffer.trim()) handleLine(buffer);
    if (!completedResponse || typeof completedResponse !== 'object') {
      throw new Error('The AI progress stream ended before the result was ready. Please try again.');
    }
    return completedResponse;
  } finally {
    clearTimeout(timeout);
    requestSignal?.removeEventListener('abort', abortRequest);
    if (showNetworkLoader) stopNetworkLoader();
  }
}

export function setToken(token) {
  localStorage.setItem('gs_token', token);
}

export function clearToken() {
  localStorage.removeItem('gs_token');
  localStorage.removeItem('gs_user');
}

export function saveUser(user) {
  localStorage.setItem('gs_user', JSON.stringify(user));
}

export function getUser() {
  try {
    return JSON.parse(localStorage.getItem('gs_user') || 'null');
  } catch {
    return null;
  }
}

/** Public URL for uploaded images (same host as API) */
export function storageUrl(relativePath) {
  if (!relativePath) return '';
  if (relativePath.startsWith('http')) return relativePath;
  return `${API_BASE}/storage/${relativePath.replace(/^\/?storage\//, '')}`;
}
