import { api, setToken, clearToken, saveUser, getUser } from '../utils/api.js';

export async function register(payload) {
  const res = await api('/auth/register', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
  if (res.data?.token) {
    setToken(res.data.token);
    saveUser(res.data.user);
  }
  return res;
}

export async function login(email, password) {
  const res = await api('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  });
  if (res.data?.token) {
    setToken(res.data.token);
    saveUser(res.data.user);
  }
  return res;
}

export async function requestPasswordReset(email) {
  return api('/auth/forgot-password', {
    method: 'POST',
    body: JSON.stringify({ email }),
  });
}

export async function resetPassword(token, password) {
  return api('/auth/reset-password', {
    method: 'POST',
    body: JSON.stringify({ token, password }),
  });
}

export async function fetchMe() {
  const res = await api('/auth/me');
  if (res.data) {
    saveUser(res.data);
  }
  return res.data;
}

export function logout() {
  clearToken();
  window.location.reload();
}

export function isLoggedIn() {
  return !!getUser() && !!localStorage.getItem('gs_token');
}
