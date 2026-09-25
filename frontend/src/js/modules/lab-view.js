import { el, clearChildren } from '../utils/dom-helpers.js';
import { createViewHeader, createField, createAlert, clearFieldError, setFieldError } from '../utils/ui.js';
import { isRequired, isPositiveNumber, isInList } from '../utils/validators.js';
import { showToast } from '../utils/toast.js';
import { formatDate, formatState } from '../utils/formatters.js';
import * as lab from '../services/lab-service.js';

const STATE_OPTIONS = [
  { value: 'solido', text: 'Sólido' },
  { value: 'liquido', text: 'Líquido' },
  { value: 'gas', text: 'Gas' },
];

export function renderLab(container) {
  clearChildren(container);
  container.appendChild(createViewHeader(
    'Laboratorio',
    'Registra muestras para estudiar su densidad y probarlas experimentalmente.'
  ));

  const listCard = el('section', 'cajon');
  listCard.appendChild(el('h3', '', 'Mis muestras'));
  const listBody = el('div');
  listBody.id = 'listaMuestras';
  listCard.appendChild(listBody);
  container.appendChild(listCard);

  const formCard = el('section', 'cajon');
  formCard.appendChild(el('h3', '', 'Nueva muestra'));
  formCard.appendChild(buildSampleForm(() => loadSamples()));
  container.appendChild(formCard);

  loadSamples();
}

function buildSampleForm(onCreated) {
  const form = el('form', 'formulario');
  const nameField = createField({ id: 'muestraNombre', label: 'Nombre de la muestra', required: true });
  const massField = createField({ id: 'muestraMasa', label: 'Masa (g)', type: 'number', placeholder: 'Ej: 25', required: true });
  const volumeField = createField({ id: 'muestraVolumen', label: 'Volumen (mL)', type: 'number', placeholder: 'Ej: 50', required: true });
  const stateField = createField({
    id: 'muestraEstado', label: 'Estado físico', type: 'select', options: STATE_OPTIONS,
  });
  const messageBox = el('div');
  const submit = el('button', 'btn btn-primario', 'Registrar muestra');
  submit.type = 'submit';

  form.appendChild(nameField.wrapper);
  form.appendChild(massField.wrapper);
  form.appendChild(volumeField.wrapper);
  form.appendChild(stateField.wrapper);
  form.appendChild(messageBox);
  form.appendChild(submit);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearChildren(messageBox);
    if (!validateSampleForm(nameField, massField, volumeField)) {
      return;
    }
    submit.disabled = true;
    try {
      await lab.createSample({
        name: nameField.input.value.trim(),
        mass: Number(massField.input.value),
        volume: Number(volumeField.input.value),
        state: stateField.input.value,
      });
      nameField.input.value = '';
      massField.input.value = '';
      volumeField.input.value = '';
      showToast('Muestra registrada');
      onCreated();
    } catch (error) {
      messageBox.appendChild(createAlert(error.message));
    } finally {
      submit.disabled = false;
    }
  });

  return form;
}

function validateSampleForm(nameField, massField, volumeField) {
  clearFieldError(nameField);
  clearFieldError(massField);
  clearFieldError(volumeField);
  let valid = true;

  if (!isRequired(nameField.input.value)) {
    setFieldError(nameField, 'Escribe un nombre');
    valid = false;
  }
  if (!isPositiveNumber(massField.input.value)) {
    setFieldError(massField, 'La masa debe ser mayor que 0');
    valid = false;
  }
  if (!isPositiveNumber(volumeField.input.value)) {
    setFieldError(volumeField, 'El volumen debe ser mayor que 0');
    valid = false;
  }
  if (!isInList(stateValue(), STATE_OPTIONS.map((option) => option.value))) {
    valid = false;
  }
  return valid;
}

function stateValue() {
  const select = document.getElementById('muestraEstado');
  return select ? select.value : '';
}

async function loadSamples() {
  const listBody = document.getElementById('listaMuestras');
  if (!listBody) {
    return;
  }
  try {
    const samples = await lab.listSamples();
    listBody.replaceChildren(buildSamplesTable(samples));
  } catch (error) {
    listBody.replaceChildren(createAlert(error.message));
  }
}

function buildSamplesTable(samples) {
  if (samples.length === 0) {
    return el('p', 'estado-vacio', 'Aún no registras muestras en el laboratorio.');
  }

  const table = el('table', 'tabla');
  const thead = el('thead');
  const headerRow = el('tr');
  for (const column of ['Muestra', 'Masa (g)', 'Volumen (mL)', 'Densidad (g/mL)', 'Estado', 'Fecha', 'Acciones']) {
    headerRow.appendChild(el('th', '', column));
  }
  thead.appendChild(headerRow);
  table.appendChild(thead);

  const tbody = el('tbody');
  for (const sample of samples) {
    const row = el('tr');
    row.appendChild(el('td', '', sample.name));
    row.appendChild(el('td', '', String(sample.mass)));
    row.appendChild(el('td', '', String(sample.volume)));
    row.appendChild(el('td', '', String(sample.density)));
    row.appendChild(el('td', '', formatState(sample.state)));

    const dateCell = el('td', '', formatDate(sample.created_at));
    row.appendChild(dateCell);

    const actionsCell = el('td');
    const deleteButton = el('button', 'btn btn-peligro btn-icono', '🗑');
    deleteButton.title = 'Eliminar muestra';
    deleteButton.addEventListener('click', () => deleteSampleRow(sample.id));
    actionsCell.appendChild(deleteButton);
    row.appendChild(actionsCell);

    tbody.appendChild(row);
  }
  table.appendChild(tbody);
  return table;
}

async function deleteSampleRow(sampleId) {
  try {
    await lab.deleteSample(sampleId);
    showToast('Muestra eliminada');
    loadSamples();
  } catch (error) {
    showToast(error.message, 'error');
  }
}