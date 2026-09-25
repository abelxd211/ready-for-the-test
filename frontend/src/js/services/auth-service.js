import { request } from './api.js';

export function registerUser({ fullName, email, password, role }) {
  return request('/api/auth/register', {
    method: 'POST',
    body: {
      full_name: fullName,
      email,
      password,
      role,
    },
  });
}

export function login({ email, password }) {
  return request('/api/auth/login', {
    method: 'POST',
    body: { email, password },
  });
}

export function getMe() {
  return request('/api/me');
}