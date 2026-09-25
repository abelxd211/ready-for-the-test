export function getElement(id) {
  const element = document.getElementById(id);
  if (!element) {
    throw new Error(`Elemento no encontrado: ${id}`);
  }
  return element;
}

export function el(tagName, className = '', text = '') {
  const node = document.createElement(tagName);
  if (className !== '') {
    node.className = className;
  }
  if (text !== '') {
    node.textContent = text;
  }
  return node;
}

export function clearChildren(node) {
  node.replaceChildren();
}

export function escapeHtml(value) {
  const replacements = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  };
  return String(value).replace(/[&<>"']/g, (character) => replacements[character]);
}

export function createHistoryItem(text, onSelect) {
  const item = document.createElement('li');
  item.textContent = text;
  item.addEventListener('click', () => onSelect(text));
  return item;
}