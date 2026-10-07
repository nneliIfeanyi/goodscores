import { api, storageUrl } from '../../utils/api.js';
import { toast } from '../../utils/toast.js';
import { confirmModal, inputModal } from '../../utils/modal.js';
import { requireAuthentication } from '../../utils/authGate.js';

let diagrams = [];

const AUDIENCE_OPTIONS = [
  { value: '', label: 'General classroom' },
  { value: 'early_learners', label: 'Early learners (ages 5-8)' },
  { value: 'upper_primary', label: 'Upper primary (ages 9-11)' },
  { value: 'junior_secondary', label: 'Junior secondary (JSS1-JSS3)' },
  { value: 'senior_secondary', label: 'Senior secondary (SS1-SS3)' },
];

function esc(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function readAsDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(String(reader.result || ''));
    reader.onerror = () => reject(reader.error || new Error('Could not read file'));
    reader.readAsDataURL(file);
  });
}

async function loadDiagrams() {
  const res = await api('/diagrams');
  diagrams = res.data || [];
}

function getDiagramContext(diagram) {
  const context = diagram?.diagram_spec?.requested_context || {};
  return {
    learner_level: String(context.learner_level || ''),
    required_labels: Array.isArray(context.required_labels) ? context.required_labels : [],
    orientation: String(context.orientation || ''),
    exclude: String(context.exclude || ''),
    audience_profile: String(context.audience_profile || ''),
  };
}

function audienceOptionsMarkup(selected = '') {
  return AUDIENCE_OPTIONS.map((option) => `<option value="${esc(option.value)}"${option.value === selected ? ' selected' : ''}>${esc(option.label)}</option>`).join('');
}

function promptFieldMarkup(id, value = '', placeholder = 'Describe what learners should see') {
  const guideId = `${id}-guide`;
  return `
    <div class="space-y-1">
      <div class="flex items-center gap-1" style="display:flex;align-items:center;gap:.25rem;">
        <label for="${esc(id)}" class="text-xs font-medium">Prompt</label>
        <span class="relative inline-flex" data-prompt-guide style="position:relative;display:inline-flex;">
          <button type="button" data-help-toggle aria-label="Prompt format guide" aria-describedby="${esc(guideId)}" aria-expanded="false" title="Prompt format guide" class="inline-flex h-5 w-5 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300 dark:hover:bg-gray-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7.5" stroke-width="1.5"/><path d="M10 9v5m0-8h.01" stroke-linecap="round" stroke-width="1.7"/></svg>
          </button>
          <span id="${esc(guideId)}" data-help-tooltip role="tooltip" class="rounded-lg border border-gray-200 bg-white p-3 text-xs font-normal leading-relaxed text-gray-700 shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" style="position:fixed;left:0;top:0;z-index:120;box-sizing:border-box;width:min(20rem,calc(100vw - 2rem));visibility:hidden;opacity:0;pointer-events:none;transition:opacity 120ms ease;">
            <strong class="block mb-1 text-gray-900 dark:text-white">Tell us the format you need</strong>
            <span class="block">For math, geometry, graphs, or exact positions, include <strong>“SVG diagram”</strong> in your prompt.</span>
            <span class="block mt-1">For a scene or concept where exact measurements do not matter, include <strong>“illustration”</strong>.</span>
            <span class="block mt-2 text-gray-500 dark:text-gray-300">Example: “SVG diagram of a football pitch with exact player positions” or “illustration of children playing football.”</span>
          </span>
        </span>
      </div>
      <textarea id="${esc(id)}" data-description rows="3" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="${esc(placeholder)}">${esc(value)}</textarea>
    </div>`;
}

function bindPromptGuide(root) {
  const guide = root.querySelector('[data-prompt-guide]');
  const toggle = guide?.querySelector('[data-help-toggle]');
  const tooltip = guide?.querySelector('[data-help-tooltip]');
  if (!guide || !toggle || !tooltip) return;

  let pinned = false;
  const setOpen = (open) => {
    if (open) {
      const buttonRect = toggle.getBoundingClientRect();
      const width = Math.min(320, window.innerWidth - 32);
      tooltip.style.width = `${width}px`;
      tooltip.style.left = `${Math.max(16, Math.min(buttonRect.left, window.innerWidth - width - 16))}px`;
      let top = buttonRect.bottom + 8;
      if (top + tooltip.getBoundingClientRect().height > window.innerHeight - 8) {
        top = Math.max(8, buttonRect.top - tooltip.getBoundingClientRect().height - 8);
      }
      tooltip.style.top = `${top}px`;
    }
    tooltip.style.visibility = open ? 'visible' : 'hidden';
    tooltip.style.opacity = open ? '1' : '0';
    tooltip.style.pointerEvents = open ? 'auto' : 'none';
    toggle.setAttribute('aria-expanded', String(open));
  };

  guide.addEventListener('mouseenter', () => setOpen(true));
  guide.addEventListener('mouseleave', () => {
    pinned = false;
    setOpen(false);
  });
  toggle.addEventListener('focus', () => setOpen(true));
  toggle.addEventListener('blur', () => {
    if (!guide.matches(':hover')) {
      pinned = false;
      setOpen(false);
    }
  });
  toggle.addEventListener('click', () => {
    pinned = !pinned;
    setOpen(pinned);
  });
}

function renderCards() {
  const list = document.getElementById('diagram-list');
  if (!list) return;
  if (!diagrams.length) {
    list.innerHTML = '<div class="text-center py-10 text-sm text-gray-400">No diagrams yet. Create one from prompt or upload from your device.</div>';
    return;
  }

  list.innerHTML = diagrams.map((item) => {
    const spec = item.diagram_spec || {};
    const isSvg = item.mode === 'precise_diagram';
    const preview = isSvg && spec.svg
      ? `<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-2 h-40 overflow-auto">${spec.svg}</div>`
      : (item.image_path
        ? `<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-2 h-40 flex items-center justify-center"><img src="${esc(storageUrl(item.image_path))}" alt="${esc(item.title)}" class="max-h-full max-w-full object-contain"/></div>`
        : '<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-2 h-40 flex items-center justify-center text-xs text-gray-400">No preview</div>');

    return `
      <article class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 space-y-2" data-diagram-card data-id="${item.id}">
        <div class="flex items-start justify-between gap-2">
          <div>
            <h3 class="font-semibold text-sm">${esc(item.title || 'Diagram')}</h3>
            <p class="text-[11px] text-gray-500">${item.mode === 'precise_diagram' ? 'SVG diagram' : 'Illustration'} · ${esc(item.source || 'ai')}</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="text-xs text-primary-600" data-edit-diagram="${item.id}">Edit</button>
            <button class="text-xs text-amber-600" data-regenerate-diagram="${item.id}">Regenerate</button>
            <button class="text-xs text-red-500" data-delete-diagram="${item.id}">Delete</button>
          </div>
        </div>
        ${preview}
        <p class="text-xs text-gray-500">${esc(item.description || spec.description || '')}</p>
      </article>`;
  }).join('');

  list.querySelectorAll('[data-edit-diagram]').forEach((btn) => {
    btn.addEventListener('click', async (event) => {
      event.stopPropagation();
      const id = Number(btn.dataset.editDiagram);
      const current = diagrams.find((d) => Number(d.id) === id);
      if (!current) return;
      const title = await inputModal({ title: 'Edit diagram title', label: 'Title', value: current.title || '' });
      if (title === null) return;
      const description = await inputModal({ title: 'Edit description', label: 'Description', value: current.description || '' });
      if (description === null) return;
      try {
        await api(`/diagrams/${id}`, { method: 'PUT', body: JSON.stringify({ title: title.trim() || 'Diagram', description: description.trim() }) });
        await loadDiagrams();
        renderCards();
        toast('Diagram updated', 'success');
      } catch (error) {
        toast(error.message || 'Could not update diagram', 'error');
      }
    });
  });

  list.querySelectorAll('[data-regenerate-diagram]').forEach((btn) => {
    btn.addEventListener('click', async (event) => {
      event.stopPropagation();
      const id = Number(btn.dataset.regenerateDiagram);
      const current = diagrams.find((d) => Number(d.id) === id);
      if (!current) return;
      const context = getDiagramContext(current);
      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
      overlay.innerHTML = `
        <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl p-4 space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">Regenerate diagram</h3>
            <button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">✕</button>
          </div>
          <label class="block text-xs font-medium">Title
            <input data-title class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" value="${esc(current.title || '')}" />
          </label>
          ${promptFieldMarkup('regenerate-diagram-prompt', current.description || '')}
          <label class="block text-xs font-medium">Audience
            <select data-audience class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">${audienceOptionsMarkup(context.audience_profile)}</select>
          </label>
          <label class="text-xs font-medium">Learner/class level<input data-learner class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" value="${esc(context.learner_level)}" placeholder="e.g. SS2" /></label>
          <label class="text-xs font-medium">Required labels (comma separated)<input data-labels class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" value="${esc(context.required_labels.join(', '))}" placeholder="e.g. nucleus, pseudopodia" /></label>
          <label class="text-xs font-medium">Orientation/layout<input data-orientation class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" value="${esc(context.orientation)}" placeholder="e.g. left-to-right with clear labels" /></label>
          <label class="text-xs font-medium">What must not appear<input data-exclude class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" value="${esc(context.exclude)}" placeholder="e.g. unrelated organisms" /></label>
          <div class="flex gap-2 pt-1">
            <button data-cancel class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Cancel</button>
            <button data-submit class="flex-1 py-2.5 rounded-xl bg-amber-600 text-white text-sm font-semibold">Regenerate</button>
          </div>
        </div>`;

      document.body.appendChild(overlay);
      bindPromptGuide(overlay);
      const close = () => overlay.remove();
      overlay.querySelector('[data-close]').onclick = close;
      overlay.querySelector('[data-cancel]').onclick = close;

      overlay.querySelector('[data-submit]').addEventListener('click', async () => {
        const submit = overlay.querySelector('[data-submit]');
        const description = overlay.querySelector('[data-description]').value.trim();
        if (!description) {
          toast('Prompt is required', 'error');
          return;
        }
        const labelsRaw = overlay.querySelector('[data-labels]').value.trim();
        submit.disabled = true;
        submit.textContent = 'Regenerating…';
        try {
          await api(`/diagrams/${id}`, {
            method: 'PUT',
            body: JSON.stringify({
              regenerate: true,
              source: 'ai',
              title: overlay.querySelector('[data-title]').value.trim() || 'Diagram',
              description,
              audience_profile: overlay.querySelector('[data-audience]').value.trim(),
              learner_level: overlay.querySelector('[data-learner]').value.trim(),
              required_labels: labelsRaw ? labelsRaw.split(',').map((x) => x.trim()).filter(Boolean) : [],
              orientation: overlay.querySelector('[data-orientation]').value.trim(),
              exclude: overlay.querySelector('[data-exclude]').value.trim(),
            }),
            timeoutMs: 180000,
          });
          await loadDiagrams();
          renderCards();
          toast('Diagram regenerated', 'success');
          close();
        } catch (error) {
          toast(error.message || 'Could not regenerate diagram', 'error');
        } finally {
          submit.disabled = false;
          submit.textContent = 'Regenerate';
        }
      });
    });
  });

  list.querySelectorAll('[data-delete-diagram]').forEach((btn) => {
    btn.addEventListener('click', async (event) => {
      event.stopPropagation();
      const id = Number(btn.dataset.deleteDiagram);
      if (!await confirmModal({ title: 'Delete diagram?', message: 'This will remove the diagram from your library.', confirmLabel: 'Delete', danger: true })) return;
      try {
        await api(`/diagrams/${id}`, { method: 'DELETE' });
        diagrams = diagrams.filter((d) => Number(d.id) !== id);
        renderCards();
        toast('Diagram deleted', 'success');
      } catch (error) {
        toast(error.message || 'Could not delete diagram', 'error');
      }
    });
  });
}

function openNewDiagramModal() {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl p-4 space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">New diagram</h3>
        <button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">✕</button>
      </div>
      <label class="block text-xs font-medium">Title
        <input data-title class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. Amoeba structure" />
      </label>
      ${promptFieldMarkup('new-diagram-prompt')}
      <button data-advanced-toggle type="button" class="text-xs text-primary-600 font-medium">Advanced fields</button>
      <div data-advanced class="hidden grid grid-cols-1 gap-2">
        <label class="text-xs font-medium">Audience
          <select data-audience class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">${audienceOptionsMarkup('')}</select>
        </label>
        <label class="text-xs font-medium">Learner/class level<input data-learner class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. SS2" /></label>
        <label class="text-xs font-medium">Required labels (comma separated)<input data-labels class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. nucleus, pseudopodia" /></label>
        <label class="text-xs font-medium">Orientation/layout<input data-orientation class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. left-to-right with clear labels" /></label>
        <label class="text-xs font-medium">What must not appear<input data-exclude class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. unrelated organisms" /></label>
      </div>
      <div class="flex gap-2 pt-1">
        <button data-cancel class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Cancel</button>
        <button data-submit class="flex-1 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold">Create diagram</button>
      </div>
    </div>`;

  document.body.appendChild(overlay);
  bindPromptGuide(overlay);
  const close = () => overlay.remove();
  overlay.querySelector('[data-close]').onclick = close;
  overlay.querySelector('[data-cancel]').onclick = close;

  overlay.querySelector('[data-advanced-toggle]').addEventListener('click', () => {
    overlay.querySelector('[data-advanced]').classList.toggle('hidden');
  });

  overlay.querySelector('[data-submit]').addEventListener('click', async () => {
    const submit = overlay.querySelector('[data-submit]');
    const description = overlay.querySelector('[data-description]').value.trim();
    if (!description) {
      toast('Prompt is required', 'error');
      return;
    }

    submit.disabled = true;
    submit.textContent = 'Creating…';
    try {
      const labelsRaw = overlay.querySelector('[data-labels]').value.trim();
      await api('/diagrams', {
        method: 'POST',
        body: JSON.stringify({
          source: 'ai',
          title: overlay.querySelector('[data-title]').value.trim() || 'Diagram',
          description,
          audience_profile: overlay.querySelector('[data-audience]').value.trim(),
          learner_level: overlay.querySelector('[data-learner]').value.trim(),
          required_labels: labelsRaw ? labelsRaw.split(',').map((x) => x.trim()).filter(Boolean) : [],
          orientation: overlay.querySelector('[data-orientation]').value.trim(),
          exclude: overlay.querySelector('[data-exclude]').value.trim(),
        }),
        timeoutMs: 180000,
      });
      await loadDiagrams();
      renderCards();
      toast('Diagram created', 'success');
      close();
    } catch (error) {
      toast(error.message || 'Could not create diagram', 'error');
    } finally {
      submit.disabled = false;
      submit.textContent = 'Create diagram';
    }
  });
}

function openUploadModal() {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-xl p-4 space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Upload diagram</h3>
        <button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">✕</button>
      </div>
      <label class="block text-xs font-medium">Title
        <input data-title class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="e.g. Amoeba hand-drawn" />
      </label>
      <label class="block text-xs font-medium">Description (optional)
        <textarea data-description rows="2" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm"></textarea>
      </label>
      <input data-file type="file" accept="image/*" class="w-full text-sm" />
      <div class="flex gap-2">
        <button data-cancel class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Cancel</button>
        <button data-submit class="flex-1 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold">Upload</button>
      </div>
    </div>`;

  document.body.appendChild(overlay);
  const close = () => overlay.remove();
  overlay.querySelector('[data-close]').onclick = close;
  overlay.querySelector('[data-cancel]').onclick = close;

  overlay.querySelector('[data-submit]').addEventListener('click', async () => {
    const file = overlay.querySelector('[data-file]').files?.[0];
    if (!file) {
      toast('Select an image to upload', 'error');
      return;
    }
    const submit = overlay.querySelector('[data-submit]');
    submit.disabled = true;
    submit.textContent = 'Uploading…';
    try {
      const dataUrl = await readAsDataUrl(file);
      await api('/diagrams', {
        method: 'POST',
        body: JSON.stringify({
          source: 'upload',
          title: overlay.querySelector('[data-title]').value.trim() || 'Uploaded diagram',
          description: overlay.querySelector('[data-description]').value.trim(),
          image_data_url: dataUrl,
        }),
      });
      await loadDiagrams();
      renderCards();
      toast('Diagram uploaded', 'success');
      close();
    } catch (error) {
      toast(error.message || 'Could not upload diagram', 'error');
    } finally {
      submit.disabled = false;
      submit.textContent = 'Upload';
    }
  });
}

export async function renderDiagrams() {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Diagrams', () => renderDiagrams());
    return;
  }

  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 pb-24 space-y-4">
      <div class="flex items-center justify-between gap-2">
        <div>
          <h2 class="text-xl font-semibold">Diagrams</h2>
          <p class="text-xs text-gray-500">Create, upload, and reuse your diagrams across questions.</p>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <button id="btn-new-diagram" class="py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold">New diagram</button>
        <button id="btn-upload-diagram" class="py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-semibold">Upload from device</button>
      </div>
      <div id="diagram-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="text-center py-8 text-gray-400 text-sm">Loading diagrams…</div>
      </div>
    </div>`;

  document.getElementById('btn-new-diagram')?.addEventListener('click', openNewDiagramModal);
  document.getElementById('btn-upload-diagram')?.addEventListener('click', openUploadModal);

  try {
    await loadDiagrams();
    renderCards();
  } catch (error) {
    document.getElementById('diagram-list').innerHTML = `<div class="text-sm text-red-500">${esc(error.message || 'Could not load diagrams')}</div>`;
  }
}
