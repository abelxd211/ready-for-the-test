import { getElement, clearChildren, el } from '../utils/dom-helpers.js';
import { emit } from '../utils/events.js';
import { session } from './session.js';
import { renderProfile } from './profile-view.js';
import { renderCalculator } from './calculator-view.js';
import { renderLab } from './lab-view.js';
import { renderCatalog } from './catalog-view.js';
import { renderMixing } from './mixing-view.js';
import { renderIdentification } from './identification-view.js';
import { renderQuiz } from './quiz-view.js';
import { renderLogin, renderRegister } from './auth-view.js';

const AUTHED_ROUTES = {
  '#/perfil': renderProfile,
  '#/calculadora': renderCalculator,
  '#/laboratorio': renderLab,
  '#/catalogo': renderCatalog,
  '#/mezclas': renderMixing,
  '#/identificacion': renderIdentification,
  '#/salas': renderQuiz,
};

const PUBLIC_ROUTES = {
  '#/login': renderLogin,
  '#/registro': renderRegister,
};

const CONTEXT_BY_ROUTE = {
  '#/perfil': 'perfil',
  '#/calculadora': 'calculadora',
  '#/laboratorio': 'laboratorio',
  '#/catalogo': 'catalogo',
  '#/mezclas': 'mezclas',
  '#/identificacion': 'identificacion',
  '#/salas': 'salas',
};

export function currentHash() {
  return location.hash || '#/perfil';
}

export function navigate(hash) {
  if (location.hash === hash) {
    handleRoute();
  } else {
    location.hash = hash;
  }
}

export function startRouter() {
  window.addEventListener('hashchange', handleRoute);
  handleRoute();
}

export function contextForRoute() {
  return CONTEXT_BY_ROUTE[currentHash()] || 'general';
}

function handleRoute() {
  const hash = currentHash();
  const container = getElement('app');
  let view = AUTHED_ROUTES[hash];

  if (view && !session.isLogged) {
    location.hash = '#/login';
    return;
  }

  if (!view) {
    view = PUBLIC_ROUTES[hash];
  }

  if (session.isLogged && (hash === '#/login' || hash === '#/registro')) {
    location.hash = '#/perfil';
    return;
  }

  clearChildren(container);
  if (view) {
    view(container);
  } else {
    container.appendChild(el('p', 'estado-vacio', 'Ruta no encontrada'));
  }
  emit('route', { hash });
}