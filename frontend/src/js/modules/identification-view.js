import { el, clearChildren } from '../utils/dom-helpers.js';
import { createViewHeader, createField, createAlert, clearFieldError, setFieldError } from '../utils/ui.js';
import { isRequired, isInList, isPositiveNumber } from '../utils/validators.js';
import { showToast } from '../utils/toast.js';
import * as lab from '../services/lab-service.js';
import { session } from './session.js';

const STATE_OPTIONS = [
  { value: 'solido', text: 'Sólido' },
  { value: 'liquido', text: 'Líquido' },
  { value: 'gas', text: 'Gas' },
];

const TRANSPARENCY_OPTIONS = [
  { value: 'transparente', text: 'Transparente' },
  { value: 'opaco', text: 'Opaco' },
];

const CONDUCTIVITY_OPTIONS = [
  { value: 'conduce', text: 'Conduce electricidad' },
  { value: 'noconduce', text: 'No conduce' },
];

export function renderIdentification(container) {
  clearChildren(container);
  container.appendChild(createViewHeader(
    'Identificación de sustancias',
    'Indica las propiedades observadas para identificar una sustancia desconocida.'
  ));

  const grid = el('div', 'dos-columnas');
  const formCard = el('section', 'cajon');
  formCard.appendChild(el('h3', '', 'Propiedades observadas'));
  formCard.appendChild(buildIdentificationForm());
  grid.appendChild(formCard);

  const resultCard = el('section', 'cajon');
  resultCard.appendChild(el('h3', '', 'Resultado'));
  const result = el('div', 'bandeja-resultado');
  result.id = 'zonaIdentificacion';
  result.appendChild(el('p', '', 'Completa las propiedades y presiona Identificar.'));
  resultCard.appendChild(result);
  grid.appendChild(resultCard);

  container.appendChild(grid);
}

function buildIdentificationForm() {
  const form = el('form', 'formulario');
  const stateField = createField({ id: 'idEstado', label: 'Estado físico', type: 'select', options: STATE_OPTIONS });
  const transparencyField = createField({ id: 'idTransparencia', label: 'Transparencia', type: 'select', options: TRANSPARENCY_OPTIONS });
  const conductivityField = createField({ id: 'idConductividad', label: 'Conductividad', type: 'select', options: CONDUCTIVITY_OPTIONS });
  const densityField = createField({ id: 'idDensidad', label: 'Densidad (g/mL)', type: 'number', placeholder: 'Ej: 1.00', required: true });
  const messageBox = el('div');
  const submit = el('button', 'btn btn-primario btn-bloque', '🔍 Identificar');
  submit.type = 'submit';

  form.appendChild(stateField.wrapper);
  form.appendChild(transparencyField.wrapper);
  form.appendChild(conductivityField.wrapper);
  form.appendChild(densityField.wrapper);
  form.appendChild(messageBox);
  form.appendChild(submit);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearChildren(messageBox);
    clearFieldError(densityField);
    if (!isPositiveNumber(densityField.input.value)) {
      setFieldError(densityField, 'La densidad debe ser mayor que 0');
      return;
    }
    submit.disabled = true;
    try {
      const data = await lab.identifySample({
        state: stateField.input.value,
        transparency: transparencyField.input.value,
        conductivity: conductivityField.input.value,
        density: Number(densityField.input.value),
      });
      renderIdentificationResult(data);
      await session.reload();
      if (data.points_awarded > 0) {
        showToast(`¡+${data.points_awarded} puntos!`, 'ok');
      }
    } catch (error) {
      messageBox.appendChild(createAlert(error.message));
    } finally {
      submit.disabled = false;
    }
  });

  return form;
}

function renderIdentificationResult(data) {
  const result = document.getElementById('zonaIdentificacion');
  clearChildren(result);
  if (data.substance === null) {
    result.appendChild(el('p', 'bandeja-nombre', 'Sustancia no identificada'));
    result.appendChild(el('span', 'badge badge-rojo', 'Sin coincidencias'));
    return;
  }
  const substance = data.substance;
  result.appendChild(el('div', 'tarjeta-icono', '🔬'));
  result.appendChild(el('p', 'bandeja-nombre', substance.name));
  result.appendChild(el('span', 'badge badge-primario', `ρ ${substance.density} g/mL`));
  result.appendChild(el('p', 'bandeja-efecto', substance.state));
}