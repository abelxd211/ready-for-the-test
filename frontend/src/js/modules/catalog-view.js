import { el, clearChildren } from '../utils/dom-helpers.js';
import { createViewHeader, createAlert } from '../utils/ui.js';
import { formatState, labelFor } from '../utils/formatters.js';
import * as lab from '../services/lab-service.js';
import { MATH_TOPICS } from './math-catalog.js';

const TABS = [
  { key: 'sustancias', label: 'Sustancias', loader: () => lab.listKnownSubstances() },
  { key: 'reactivos', label: 'Reactivos', loader: () => lab.listReagents() },
  { key: 'reacciones', label: 'Reacciones', loader: () => lab.listReactions() },
  { key: 'matematicas', label: 'Matemáticas', loader: () => MATH_TOPICS },
];

const TAB_BADGES = {
  sustancias: 'Sustancias de referencia con propiedades conocidas, descripción y datos útiles.',
  reactivos: 'Reactivos de la mesa de mezclas con su color, icono y uso.',
  reacciones: 'Reacciones conocidas al mezclar pares de reactivos.',
  matematicas: 'Temas de repaso: aritmética, álgebra, geometría y proporcionalidad.',
};

export function renderCatalog(container) {
  clearChildren(container);
  container.appendChild(createViewHeader('Catálogo', 'Consulta sustancias, reactivos, reacciones y temas de matemáticas.'));

  const tabBar = el('div', 'vista-acciones');
  const content = el('section');
  container.appendChild(tabBar);
  container.appendChild(content);

  for (const tab of TABS) {
    const button = el('button', 'chip', tab.label);
    button.addEventListener('click', () => {
      switchTab(tab, button, content, tabBar);
    });
    tabBar.appendChild(button);
    if (tab.key === 'sustancias') {
      button.classList.add('is-activo');
    }
  }

  switchTab(TABS[0], tabBar.querySelector('.chip'), content, tabBar);
}

async function switchTab(tab, button, content, tabBar) {
  for (const chip of tabBar.querySelectorAll('.chip')) {
    chip.classList.remove('is-activo');
  }
  button.classList.add('is-activo');
  content.replaceChildren(el('p', 'estado-vacio', 'Cargando…'));

  try {
    const items = await tab.loader();
    content.replaceChildren(renderItems(tab.key, items));
  } catch (error) {
    content.replaceChildren(createAlert(error.message));
  }
}

function renderItems(key, items) {
  const panel = document.createElement('div');
  panel.appendChild(el('p', 'estado-vacio', TAB_BADGES[key]));

  if (items.length === 0) {
    panel.appendChild(el('p', 'estado-vacio', 'No hay datos disponibles.'));
    return panel;
  }

  const grid = el('div', 'rejilla');
  grid.classList.add('lista-horizontal');
  for (const item of items) {
    grid.appendChild(buildCard(key, item));
  }
  panel.appendChild(grid);
  return panel;
}

function buildCard(key, item) {
  const card = el('article', 'tarjeta');

  if (key === 'sustancias') {
    card.appendChild(el('div', 'tarjeta-icono', '🧪'));
    card.appendChild(el('h3', '', item.name));
    card.appendChild(el('p', 'estado-vacio', item.description));
    card.appendChild(el('p', '', `Estado: ${formatState(item.state)} · ${labelFor(item.transparency)} · ${labelFor(item.conductivity)}`));
    card.appendChild(el('span', 'badge badge-primario', `ρ ${item.density} g/mL`));
    card.appendChild(el('p', 'estado-vacio', item.detail));
  }

  if (key === 'reactivos') {
    const colorDot = el('span', 'vial-muestra');
    colorDot.style.setProperty('--vial-color', item.color_hex);
    card.appendChild(colorDot);
    card.appendChild(el('h3', '', `${item.icon} ${item.name}`));
    card.appendChild(el('p', 'estado-vacio', item.description));
    card.appendChild(el('span', 'badge badge-ambar', item.id));
  }

  if (key === 'reacciones') {
    card.appendChild(el('div', 'tarjeta-icono', '⚗️'));
    card.appendChild(el('h3', '', item.result_name));
    card.appendChild(el('p', '', `${item.reagent_a_id} + ${item.reagent_b_id}`));
    card.appendChild(el('p', '', item.description));
    card.appendChild(el('span', 'badge badge-verde', labelFor(item.effect)));
  }

  if (key === 'matematicas') {
    card.appendChild(buildMathTopicCard(item));
  }

  return card;
}

function buildMathTopicCard(topic) {
  const wrapper = el('div');
  wrapper.appendChild(el('div', 'tarjeta-icono', topic.icon));
  wrapper.appendChild(el('h3', '', topic.title));
  wrapper.appendChild(el('span', 'badge badge-primario', topic.badge));

  for (const item of topic.items) {
    const itemBox = el('div', 'cajon');
    itemBox.appendChild(el('p', '', item.name));
    itemBox.appendChild(el('p', 'estado-vacio', item.formula));
    itemBox.appendChild(el('p', 'estado-vacio', item.note));
    wrapper.appendChild(itemBox);
  }

  return wrapper;
}