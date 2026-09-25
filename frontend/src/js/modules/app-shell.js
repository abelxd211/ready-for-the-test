import { getElement, el, clearChildren } from '../utils/dom-helpers.js';
import { on } from '../utils/events.js';
import { session } from './session.js';
import { navigate, currentHash } from './router.js';

const THEME_KEY = 'rft_tema';
const NAV_ITEMS = [
  { hash: '#/perfil', label: 'Mi perfil' },
  { hash: '#/calculadora', label: 'Calculadora' },
  { hash: '#/laboratorio', label: 'Laboratorio' },
  { hash: '#/catalogo', label: 'Catálogo' },
  { hash: '#/mezclas', label: 'Mezclas' },
  { hash: '#/identificacion', label: 'Identificación' },
  { hash: '#/salas', label: 'Salas' },
];

let isRendered = false;

export function renderShell() {
  isRendered = true;
  on('session', refreshHeaderUi);
  on('route', ({ hash }) => markActiveRoute(hash));
  applyPersistedTheme();
  refreshHeaderUi();
}

function refreshHeaderUi() {
  if (!isRendered) {
    return;
  }
  const header = getElement('appHeader');
  header.replaceChildren(buildHeader());
}

function buildHeader() {
  const header = el('header', 'app-header');

  const brand = el('a', 'app-brand');
  brand.href = '#/perfil';
  brand.appendChild(el('span', 'app-brand-icon', '🧪'));
  brand.appendChild(el('span', '', 'Ready for the Test?'));

  const nav = el('nav', 'app-nav');
  if (session.isLogged) {
    for (const item of NAV_ITEMS) {
      nav.appendChild(buildNavLink(item));
    }
  }

  const right = el('div', 'app-header-derecha');
  right.appendChild(buildThemeButton());
  if (session.isLogged) {
    right.appendChild(buildScorePill());
    right.appendChild(buildSessionPill());
    right.appendChild(buildLogoutButton());
  } else {
    right.appendChild(buildLinkButton('Ingresar', '#/login', 'btn btn-primario'));
    right.appendChild(buildLinkButton('Registrarse', '#/registro', 'btn btn-contorno'));
  }

  header.appendChild(brand);
  header.appendChild(nav);
  header.appendChild(right);
  return header;
}

function buildNavLink(item) {
  const link = el('button', `nav-link${item.hash === currentHash() ? ' is-active' : ''}`, item.label);
  link.setAttribute('data-ruta', item.hash);
  link.addEventListener('click', () => navigate(item.hash));
  return link;
}

function buildThemeButton() {
  const button = el('button', 'btn btn-icono', document.body.classList.contains('tema-oscuro') ? '☀️' : '🌙');
  button.setAttribute('aria-label', 'Cambiar tema');
  button.addEventListener('click', toggleTheme);
  return button;
}

function buildScorePill() {
  const pill = el('span', 'score-pill');
  pill.appendChild(el('span', '', '⭐'));
  pill.appendChild(el('strong', '', String(session.user.points)));
  pill.appendChild(el('span', '', 'puntos'));
  return pill;
}

function buildSessionPill() {
  const pill = el('span', 'session-pill');
  pill.appendChild(el('span', '', session.user.full_name.split(' ')[0]));
  return pill;
}

function buildLogoutButton() {
  const button = el('button', 'btn btn-icono', '⎋');
  button.title = 'Cerrar sesión';
  button.setAttribute('aria-label', 'Cerrar sesión');
  button.addEventListener('click', () => {
    session.logout();
    navigate('#/login');
  });
  return button;
}

function buildLinkButton(label, hash, className) {
  const link = el('a', className, label);
  link.href = hash;
  return link;
}

function markActiveRoute(hash) {
  const links = document.querySelectorAll('.nav-link');
  for (const link of links) {
    link.classList.toggle('is-active', link.getAttribute('data-ruta') === hash);
  }
}

function toggleTheme() {
  const isDark = document.body.classList.toggle('tema-oscuro');
  localStorage.setItem(THEME_KEY, isDark ? 'oscuro' : 'claro');
  refreshHeaderUi();
}

function applyPersistedTheme() {
  if (localStorage.getItem(THEME_KEY) === 'oscuro') {
    document.body.classList.add('tema-oscuro');
  }
}

export function refreshHeader() {
  refreshHeaderUi();
}