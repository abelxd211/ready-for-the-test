import { getToken, clearToken } from '../utils/storage.js';

export const API_BASE = '';

export class ApiError extends Error {
  constructor(message, status) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
  }
}

export async function request(path, { method = 'GET', body } = {}) {
  const headers = { 'Content-Type': 'application/json' };
  const token = getToken();
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const options = {
    method,
    headers,
    body: body === undefined ? undefined : JSON.stringify(body),
  };

  const response = await fetch(`${API_BASE}${path}`, options);

  let payload = null;
  try {
    payload = await response.json();
  } catch {
    payload = null;
  }

  if (!response.ok) {
    if (response.status === 401) {
      clearToken();
    }
    throw new ApiError(payload && payload.message ? payload.message : 'Error de conexión', response.status);
  }

  return payload && payload.data !== undefined ? payload.data : payload;
}