import { request } from './api.js';

export function askAssistant({ message, context }) {
  return request('/api/assistant/query', {
    method: 'POST',
    body: { message, context },
  });
}

export function getHelp(context) {
  return request(`/api/assistant/help?context=${encodeURIComponent(context)}`);
}

export function getContextData() {
  return request('/api/assistant/context');
}