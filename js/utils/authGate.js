import { isLoggedIn } from '../modules/auth.js';
import { renderLogin } from '../modules/pages/login.js';
import { toast } from './toast.js';

let pendingAction = null;

export function requireAuthentication(feature, action) {
  if (isLoggedIn()) return Promise.resolve(action());
  pendingAction = action;
  renderLogin({ message: `${feature} requires an online authenticated account. Sign in to continue.` });
  return Promise.resolve(false);
}

export async function resumeAuthenticationAction() {
  const action = pendingAction;
  pendingAction = null;
  if (!action) return false;
  try {
    await action();
    return true;
  } catch (error) {
    toast(error.message || 'Could not resume that action', 'error');
    return false;
  }
}
