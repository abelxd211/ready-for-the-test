const listeners = {};

export function on(eventName, handler) {
  if (!listeners[eventName]) {
    listeners[eventName] = [];
  }
  listeners[eventName].push(handler);
}

export function emit(eventName, payload) {
  const eventListeners = listeners[eventName] || [];
  for (const handler of eventListeners) {
    handler(payload);
  }
}