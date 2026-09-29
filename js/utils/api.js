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
