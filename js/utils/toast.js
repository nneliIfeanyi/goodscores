/** Lightweight toast notifications */
export function toast(message, type = 'info') {
  const colors = {
    info: 'bg-gradient-to-r from-slate-800 via-slate-700 to-cyan-900 text-white',
    success: 'bg-gradient-to-r from-teal-700 via-cyan-700 to-emerald-700 text-white',
    error: 'bg-gradient-to-r from-red-700 via-rose-700 to-orange-700 text-white',
    warn: 'bg-gradient-to-r from-amber-600 via-orange-600 to-yellow-700 text-white',
  };
  const el = document.createElement('div');
  el.className = `pointer-events-none fixed bottom-24 left-1/2 -translate-x-1/2 z-[100] max-w-sm w-[90%] px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-center ${colors[type] || colors.info}`;
  el.textContent = message;
  document.body.appendChild(el);
  setTimeout(() => {
    el.style.opacity = '0';
    el.style.transition = 'opacity 0.4s ease';
    setTimeout(() => el.remove(), 400);
  }, 4500);
}
