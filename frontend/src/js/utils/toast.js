import { el, getElement } from './dom-helpers.js';

const HIDE_DELAY = 2400;
const FADE_DELAY = 320;

export function showToast(message, type = 'ok') {
  const layer = getElement('toastLayer');
  const toast = el('div', `toast toast-${type}`, message);
  layer.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('is-salir');
    setTimeout(() => toast.remove(), FADE_DELAY);
  }, HIDE_DELAY);
}