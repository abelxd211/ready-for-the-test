import { el, clearChildren } from '../utils/dom-helpers.js';
import { createViewHeader, createAlert } from '../utils/ui.js';
import { showToast } from '../utils/toast.js';
import { labelFor } from '../utils/formatters.js';
import * as lab from '../services/lab-service.js';
import { session } from './session.js';

let reagentCache = null;

export function renderMixing(container) {
  clearChildren(container);
  container.appendChild(createViewHeader(
    'Mesa de mezclas',
    'Combina dos reactivos y observa la reacción que se produce.'
  ));

  const grid = el('div', 'dos-columnas');

  const shelfCard = el('section', 'cajon');
  shelfCard.appendChild(el('h3', '', 'Reactivos disponibles'));
  const shelf = el('div', 'estante');
  shelf.id = 'estanteReactivos';
  shelfCard.appendChild(shelf);
  grid.appendChild(shelfCard);

  const mixCard = el('section', 'cajon');
  mixCard.appendChild(el('h3', '', 'Mezcla seleccionada'));
  const slots = el('div', 'estante');
  slots.id = 'slotsMezcla';
  mixCard.appendChild(slots);

  const result = el('div', 'bandeja-resultado');
  result.id = 'zonaResultado';
  result.appendChild(el('p', '', 'Selecciona dos reactivos y presiona Mezclar.'));
  mixCard.appendChild(result);

  const mixButton = el('button', 'btn btn-ambar btn-bloque', '🧪 Mezclar');
  mixButton.id = 'btnMezclar';
  mixButton.disabled = true;
  mixCard.appendChild(mixButton);

  grid.appendChild(mixCard);
  container.appendChild(grid);

  initMixingArea(shelf, slots, result, mixButton);
}

function initMixingArea(shelf, slots, result, mixButton) {
  const selected = [];
  let reagents = reagentCache || [];

  function renderShelf() {
    clearChildren(shelf);
    for (const reagent of reagents) {
      const vial = buildVial(reagent);
      const isSelected = selected.includes(reagent.id);
      vial.classList.toggle('is-seleccionado', isSelected);
      vial.addEventListener('click', () => toggleSelection(reagent));
      shelf.appendChild(vial);
    }
    if (reagents.length === 0) {
      shelf.appendChild(el('p', 'estado-vacio', 'No hay reactivos disponibles.'));
    }
  }

  function renderSlots() {
    clearChildren(slots);
    for (let i = 0; i < 2; i++) {
      const slot = el('div', 'vial');
      slot.classList.add('is-slot');
      if (selected[i]) {
        const reagent = reagents.find((item) => item.id === selected[i]);
        const dot = buildColorDot(reagent.color_hex);
        slot.appendChild(dot);
        slot.appendChild(el('span', '', reagent.name));
      } else {
        slot.appendChild(el('span', '', `Elegir reactivo ${i === 0 ? 'A' : 'B'}`));
      }
      slots.appendChild(slot);
    }
    mixButton.disabled = selected.length < 2;
  }

  function toggleSelection(reagent) {
    const index = selected.indexOf(reagent.id);
    if (index >= 0) {
      selected.splice(index, 1);
    } else if (selected.length < 2) {
      selected.push(reagent.id);
    } else {
      showToast('Ya seleccionaste dos reactivos', 'error');
      return;
    }
    renderShelf();
    renderSlots();
  }

  async function doMix() {
    if (selected.length < 2) {
      return;
    }
    mixButton.disabled = true;
    clearChildren(result);
    result.appendChild(el('p', '', 'Mezclando…'));
    try {
      const data = await lab.mixReagents({ reagentAId: selected[0], reagentBId: selected[1] });
      renderResult(data);
      await session.reload();
      if (data.points_awarded > 0) {
        showToast(`¡+${data.points_awarded} puntos!`, 'ok');
      }
    } catch (error) {
      result.replaceChildren(createAlert(error.message));
    } finally {
      mixButton.disabled = selected.length < 2;
    }
  }

  function renderResult(data) {
    clearChildren(result);
    if (data.reaction === null) {
      result.appendChild(el('p', 'bandeja-nombre', 'No se produjo reacción visible'));
      result.appendChild(el('span', 'badge badge-rojo', 'Sin efecto'));
      return;
    }
    const colorDot = buildColorDot(data.reaction.result_color_hex);
    result.appendChild(colorDot);
    result.appendChild(el('p', 'bandeja-nombre', data.reaction.result_name));
    result.appendChild(el('span', 'badge badge-verde', labelFor(data.effect)));
    result.appendChild(el('p', 'bandeja-efecto', data.reaction.description));
  }

  mixButton.addEventListener('click', doMix);

  async function load() {
    try {
      if (reagentCache === null) {
        reagentCache = await lab.listReagents();
      }
      reagents = reagentCache;
      renderShelf();
      renderSlots();
    } catch (error) {
      shelf.replaceChildren(createAlert(error.message));
    }
  }

  load();
}

function buildVial(reagent) {
  const vial = el('button', 'vial');
  const dot = buildColorDot(reagent.color_hex);
  vial.appendChild(dot);
  vial.appendChild(el('span', '', `${reagent.icon} ${reagent.name}`));
  return vial;
}

function buildColorDot(colorHex) {
  const dot = el('span', 'vial-muestra');
  dot.style.setProperty('--vial-color', colorHex);
  return dot;
}