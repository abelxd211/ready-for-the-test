import { getElement, createHistoryItem } from '../utils/dom-helpers.js';

const MAX_HISTORY_ENTRIES = 15;

export class CalculatorHistory {
  constructor(listElementId, onSelectEntry) {
    this.listElement = getElement(listElementId);
    this.onSelectEntry = onSelectEntry;
    this.entries = [];
  }

  add(entry) {
    this.entries.push(entry);
    if (this.entries.length > MAX_HISTORY_ENTRIES) {
      this.entries.shift();
    }
    this.render();
  }

  seed(entries) {
    this.entries = [...entries];
    this.render();
  }

  clear() {
    this.entries = [];
    this.render();
  }

  render() {
    this.listElement.innerHTML = '';
    for (let i = this.entries.length - 1; i >= 0; i--) {
      const item = createHistoryItem(this.entries[i].text, this.onSelectEntry);
      this.listElement.appendChild(item);
    }
  }
}