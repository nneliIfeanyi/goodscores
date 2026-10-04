export function confirmModal({ title = 'Please confirm', message, confirmLabel = 'Confirm', danger = false }) {
  return new Promise((resolve) => {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[120] bg-black/50 flex items-end sm:items-center justify-center p-4';
    overlay.innerHTML = `
      <div class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-2xl shadow-xl p-5" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <h3 id="confirm-title" class="font-semibold">${escapeHtml(title)}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">${escapeHtml(message)}</p>
        <div class="flex gap-2 mt-5">
          <button type="button" data-cancel class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Cancel</button>
          <button type="button" data-confirm class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold ${danger ? 'bg-red-600 hover:bg-red-700' : 'bg-primary-600 hover:bg-primary-700'}">${escapeHtml(confirmLabel)}</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const finish = (value) => { overlay.remove(); resolve(value); };
    overlay.querySelector('[data-cancel]').onclick = () => finish(false);
    overlay.querySelector('[data-confirm]').onclick = () => finish(true);
  });
}

export function inputModal({ title = 'Enter a value', message = '', label = 'Value', value = '', type = 'text', placeholder = '', confirmLabel = 'Save' }) {
  return new Promise((resolve) => {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[120] bg-black/50 flex items-end sm:items-center justify-center p-4';
    overlay.innerHTML = `
      <div class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-2xl shadow-xl p-5" role="dialog" aria-modal="true" aria-labelledby="input-title">
        <h3 id="input-title" class="font-semibold">${escapeHtml(title)}</h3>
        ${message ? `<p class="text-sm text-gray-600 dark:text-gray-300 mt-2">${escapeHtml(message)}</p>` : ''}
        <form data-input-form class="mt-4">
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-300" for="modal-input">${escapeHtml(label)}</label>
          <input id="modal-input" name="value" type="${escapeHtml(type)}" value="${escapeHtml(value)}" placeholder="${escapeHtml(placeholder)}" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" />
          <div class="flex gap-2 mt-5">
            <button type="button" data-cancel class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Cancel</button>
            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold">${escapeHtml(confirmLabel)}</button>
          </div>
        </form>
      </div>`;
    document.body.appendChild(overlay);
    const input = overlay.querySelector('#modal-input');
    const finish = (result) => { overlay.remove(); resolve(result); };
    overlay.querySelector('[data-cancel]').onclick = () => finish(null);
    overlay.querySelector('[data-input-form]').onsubmit = (event) => {
      event.preventDefault();
      finish(input.value);
    };
    input.focus();
    input.select();
  });
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
