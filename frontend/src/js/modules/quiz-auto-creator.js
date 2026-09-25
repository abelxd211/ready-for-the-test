import { el, clearChildren } from '../utils/dom-helpers.js';
import { createAlert, createField, clearFieldError, setFieldError } from '../utils/ui.js';
import { showToast } from '../utils/toast.js';
import { isInRange } from '../utils/validators.js';
import * as quiz from '../services/quiz-service.js';

export function buildAutoCreator(onCreated) {
  const form = el('form', 'formulario');
  const topicField = createField({ id: 'salaAutoTema', label: 'Tema', type: 'select', required: true });
  topicField.input.appendChild(el('option', '', 'Cargando temas…'));

  const titleField = createField({ id: 'salaAutoTitulo', label: 'Título de la sala (opcional)' });
  titleField.input.maxLength = 120;

  const countField = createField({
    id: 'salaAutoCantidad', label: 'Cantidad de preguntas (1 a 10)', type: 'number', required: true,
  });
  countField.input.value = '5';
  countField.input.min = '1';
  countField.input.max = '10';

  const pointsField = createField({
    id: 'salaAutoPuntos', label: 'Puntos por pregunta (1 a 100)', type: 'number', required: true,
  });
  pointsField.input.value = '10';
  pointsField.input.min = '1';
  pointsField.input.max = '100';

  const messageBox = el('div');
  const submit = el('button', 'btn btn-primario', '✨ Generar con IA');
  submit.type = 'submit';

  form.appendChild(topicField.wrapper);
  form.appendChild(titleField.wrapper);
  form.appendChild(countField.wrapper);
  form.appendChild(pointsField.wrapper);
  form.appendChild(messageBox);
  form.appendChild(submit);

  loadAutoTopics(topicField, messageBox);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearChildren(messageBox);
    clearFieldError(countField);
    clearFieldError(pointsField);

    const count = Number(countField.input.value);
    const points = Number(pointsField.input.value);
    const countOk = isInRange(count, 1, 10);
    const pointsOk = isInRange(points, 1, 100);
    if (!countOk) {
      setFieldError(countField, 'Elige entre 1 y 10 preguntas');
    }
    if (!pointsOk) {
      setFieldError(pointsField, 'Puntos de 1 a 100');
    }
    if (!countOk || !pointsOk) {
      return;
    }

    submit.disabled = true;
    try {
      const topic = topicField.input.value;
      const result = await quiz.createAutoRoom({
        title: titleField.input.value.trim(),
        topic,
        questionCount: count,
        pointsValue: points,
      });
      showToast(`Sala IA creada · código ${result.room.code}`);
      titleField.input.value = '';
      onCreated();
    } catch (error) {
      messageBox.appendChild(createAlert(error.message));
    } finally {
      submit.disabled = false;
    }
  });

  return form;
}

async function loadAutoTopics(topicField, messageBox) {
  topicField.input.replaceChildren(el('option', '', 'Cargando temas…'));
  try {
    const topics = await quiz.autoRoomTopics();
    topicField.input.replaceChildren();
    for (const topic of topics) {
      const option = el('option', '', `${topic.icon} ${topic.label} (${topic.question_count} preguntas)`);
      option.value = topic.id;
      topicField.input.appendChild(option);
    }
  } catch (error) {
    messageBox.appendChild(createAlert(error.message));
  }
}