import { el, clearChildren, getElement } from '../utils/dom-helpers.js';
import { on } from '../utils/events.js';
import * as assistant from '../services/assistant-service.js';
import { session } from './session.js';
import { contextForRoute } from './router.js';

let wrapper = null;
let panelOpen = false;
let currentContext = 'general';

export function initAssistant() {
  const layer = getElement('assistantLayer');
  wrapper = el('div', 'chat-flotante');
  wrapper.id = 'asistenteFlotante';
  layer.appendChild(wrapper);

  on('session', () => {
    if (session.isLogged) {
      wrapper.replaceChildren(buildButton());
    } else {
      panelOpen = false;
      wrapper.replaceChildren();
    }
  });

  on('route', () => {
    if (panelOpen) {
      currentContext = contextForRoute();
    }
  });

  if (session.isLogged) {
    wrapper.replaceChildren(buildButton());
  }
}

function buildButton() {
  const button = el('button', 'chat-boton', '💬');
  button.setAttribute('aria-label', 'Abrir asistente');
  button.addEventListener('click', togglePanel);
  return button;
}

function togglePanel() {
  panelOpen = !panelOpen;
  wrapper.replaceChildren(buildButton());
  if (panelOpen) {
    wrapper.appendChild(buildPanel());
  }
}

function buildPanel() {
  const panel = el('div', 'chat-panel');

  const header = el('div', 'chat-cabecera');
  header.appendChild(el('h3', '', 'Asistente 🤖'));
  const closeButton = el('button', 'btn btn-icono', '✕');
  closeButton.addEventListener('click', togglePanel);
  header.appendChild(closeButton);
  panel.appendChild(header);

  const messages = el('div', 'chat-mensajes');
  messages.id = 'chatMensajes';
  const greeting = el('div', 'burbuja burbuja-bot', 'Hola, soy tu asistente. ¿En qué te ayudo?');
  messages.appendChild(greeting);
  panel.appendChild(messages);

  const suggestions = el('div', 'chat-sugerencias');
  suggestions.id = 'chatSugerencias';
  panel.appendChild(suggestions);

  const entry = el('div', 'chat-entrada');
  const input = document.createElement('input');
  input.type = 'text';
  input.id = 'chatEntradaInput';
  input.placeholder = 'Escribe tu consulta…';
  input.maxLength = 500;
  const sendButton = el('button', '', '➤');
  sendButton.setAttribute('aria-label', 'Enviar');
  entry.appendChild(input);
  entry.appendChild(sendButton);
  panel.appendChild(entry);

  sendButton.addEventListener('click', () => sendMessage(input, messages));
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      sendMessage(input, messages);
    }
  });

  loadSuggestions(suggestions, currentContext);
  return panel;
}

async function loadSuggestions(suggestions, context) {
  clearChildren(suggestions);
  try {
    const topics = await assistant.getHelp(context);
    const seen = new Set();
    const chips = [];
    for (const topic of topics) {
      for (const suggestion of topic.suggestions) {
        if (!seen.has(suggestion)) {
          seen.add(suggestion);
          chips.push(suggestion);
        }
        if (chips.length >= 5) {
          break;
        }
      }
      if (chips.length >= 5) {
        break;
      }
    }
    for (const chipText of chips) {
      plainChip(suggestions, chipText);
    }
  } catch {
    suggestions.appendChild(el('span', 'estado-vacio', 'No hay sugerencias disponibles.'));
  }
}

function plainChip(suggestions, text) {
  const chip = el('button', 'chat-chip', text);
  chip.addEventListener('click', () => {
    const messages = document.getElementById('chatMensajes');
    const input = document.getElementById('chatEntradaInput');
    submitQuery(text, input, messages);
  });
  suggestions.appendChild(chip);
}

function sendMessage(input, messages) {
  const value = input.value.trim();
  if (value === '') {
    return;
  }
  input.value = '';
  submitQuery(value, input, messages);
}

async function submitQuery(message, input, messages) {
  const contextValue = currentContext || contextForRoute();
  const userBubble = el('div', 'burbuja burbuja-usuario', message);
  messages.appendChild(userBubble);
  messages.scrollTop = messages.scrollHeight;

  const typingBubble = el('div', 'burbuja burbuja-bot', '…');
  messages.appendChild(typingBubble);
  messages.scrollTop = messages.scrollHeight;

  try {
    const result = await assistant.askAssistant({ message, context: contextValue });
    typingBubble.textContent = result.response;
    const suggestions = document.getElementById('chatSugerencias');
    updateSuggestionsState(suggestions, result.suggestions);
  } catch (error) {
    typingBubble.textContent = `Error: ${error.message}`;
  }
  messages.scrollTop = messages.scrollHeight;
}

function updateSuggestionsState(suggestions, suggestionList) {
  clearChildren(suggestions);
  if (!suggestionList) {
    return;
  }
  const messages = document.getElementById('chatMensajes');
  const input = document.getElementById('chatEntradaInput');
  for (const text of suggestionList) {
    plainChip(suggestions, text);
  }
  if (suggestionList.length > 0 && input) {
    input.placeholder = 'Escribe tu consulta o elige una sugerencia…';
  }
}