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

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
