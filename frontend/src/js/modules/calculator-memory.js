export class CalculatorMemory {
  constructor() {
    this.storedValue = 0;
  }

  add(value) {
    this.storedValue += value;
  }

  read() {
    return this.storedValue;
  }

  clear() {
    this.storedValue = 0;
  }
}
