import { getElement } from '../utils/dom-helpers.js';
import { roundResult, calculateFactorial } from '../utils/math-helpers.js';
import * as scientific from './scientific-operations.js';
import { CalculatorMemory } from './calculator-memory.js';
import { CalculatorHistory } from './calculator-history.js';

const VALID_OPERATORS = ['+', '-', '*', '/', '%', '^'];
const MAX_DIGITS = 18;

export class Calculator {
  constructor(displayId, historyListId, historySaver = null, errorReporter = null) {
    this.display = getElement(displayId);
    this.currentValue = '0';
    this.previousValue = null;
    this.selectedOperator = null;
    this.shouldResetDisplay = false;
    this.angleUnit = 'RAD';
    this.memory = new CalculatorMemory();
    this.history = new CalculatorHistory(historyListId, (entry) => this.useHistoryEntry(entry));
    this.historySaver = historySaver;
    this.errorReporter = errorReporter;
  }

  reportError(message) {
    if (this.errorReporter) {
      this.errorReporter(message);
    }
  }

  recordEntry(entry) {
    this.history.add(entry);
    if (this.historySaver) {
      this.historySaver({
        expression: entry.expression,
        result: entry.result,
        operationType: entry.operationType,
      });
    }
  }

  seedHistoryFromServer(entries) {
    this.history.seed(entries);
  }

  updateDisplay() {
    this.display.value = this.currentValue;
  }

  pressNumber(digit) {
    if (this.shouldResetDisplay) {
      this.currentValue = '';
      this.shouldResetDisplay = false;
    }
    const digitCount = this.currentValue.replace(/[^0-9]/g, '').length;
    if (digitCount >= MAX_DIGITS) return;
    this.currentValue = this.currentValue === '0' ? digit : this.currentValue + digit;
    this.updateDisplay();
  }

  pressParenthesis(symbol) {
    this.currentValue = this.currentValue === '0' ? symbol : this.currentValue + symbol;
    this.updateDisplay();
  }

  pressDecimalPoint() {
    if (this.shouldResetDisplay) {
      this.currentValue = '0';
      this.shouldResetDisplay = false;
    }
    const parts = this.currentValue.split(/[+\-*/^]/);
    const lastPart = parts[parts.length - 1];
    if (!lastPart.includes('.')) {
      this.currentValue += '.';
      this.updateDisplay();
    }
  }

  pressOperator(operator) {
    if (!VALID_OPERATORS.includes(operator)) return;
    if (this.previousValue !== null && !this.shouldResetDisplay) {
      this.calculateResult();
    }
    this.selectedOperator = operator;
    this.previousValue = this.currentValue;
    this.shouldResetDisplay = true;
  }

  calculateResult() {
    if (this.selectedOperator === null || this.previousValue === null) return;
    const first = parseFloat(this.previousValue);
    const second = parseFloat(this.currentValue);
    const result = this.computeOperation(first, second, this.selectedOperator);
    if (result === null) return;
    const rounded = roundResult(result);
    this.recordEntry({
      text: `${first} ${this.selectedOperator} ${second} = ${rounded}`,
      expression: `${first} ${this.selectedOperator} ${second}`,
      result: String(rounded),
      operationType: 'basica',
    });
    this.finishOperation(result);
  }

  computeOperation(first, second, operator) {
    switch (operator) {
      case '+': return first + second;
      case '-': return first - second;
      case '*': return first * second;
      case '/': return this.divide(first, second);
      case '%': return first % second;
      case '^': return Math.pow(first, second);
      default: return second;
    }
  }

  divide(first, second) {
    if (second === 0) {
      this.reportError('No se puede dividir entre cero');
      this.clearAll();
      return null;
    }
    return first / second;
  }

  finishOperation(result) {
    this.currentValue = roundResult(result).toString();
    this.previousValue = null;
    this.selectedOperator = null;
    this.shouldResetDisplay = true;
    this.updateDisplay();
  }

  applyFunction(functionName) {
    const value = parseFloat(this.currentValue);
    try {
      const result = this.computeFunction(functionName, value);
      const rounded = roundResult(result);
      this.recordEntry({
        text: `${functionName}(${value}) = ${rounded}`,
        expression: `${functionName}(${value})`,
        result: String(rounded),
        operationType: 'cientifica',
      });
      this.currentValue = rounded.toString();
      this.shouldResetDisplay = true;
      this.updateDisplay();
    } catch (error) {
      this.reportError(error.message);
    }
  }

  computeFunction(functionName, value) {
    const unit = this.angleUnit;
    const functionsMap = {
      sin: () => scientific.applySine(value, unit),
      cos: () => scientific.applyCosine(value, unit),
      tan: () => scientific.applyTangent(value, unit),
      asin: () => scientific.applyArcsine(value, unit),
      acos: () => scientific.applyArccosine(value, unit),
      atan: () => scientific.applyArctangent(value, unit),
      log: () => scientific.applyLogarithm(value),
      ln: () => scientific.applyNaturalLog(value),
      sqrt: () => scientific.applySquareRoot(value),
      cuadrado: () => scientific.applySquare(value),
      cubo: () => scientific.applyCube(value),
      inverso: () => scientific.applyInverse(value),
      signo: () => scientific.applySign(value),
      factorial: () => this.computeFactorial(value),
    };
    const operation = functionsMap[functionName];
    return operation ? operation() : value;
  }

  computeFactorial(value) {
    const result = calculateFactorial(value);
    if (result === null) {
      throw new RangeError('El factorial solo aplica a enteros positivos');
    }
    return result;
  }

  toggleAngleUnit(indicatorId) {
    this.angleUnit = this.angleUnit === 'RAD' ? 'DEG' : 'RAD';
    getElement(indicatorId).textContent = this.angleUnit;
  }

  saveToMemory() {
    this.memory.add(parseFloat(this.currentValue));
  }

  recallMemory() {
    this.currentValue = this.memory.read().toString();
    this.shouldResetDisplay = true;
    this.updateDisplay();
  }

  clearMemory() {
    this.memory.clear();
  }

  deleteLastDigit() {
    this.currentValue = this.currentValue.length === 1 ? '0' : this.currentValue.slice(0, -1);
    this.updateDisplay();
  }

  clearAll() {
    this.currentValue = '0';
    this.previousValue = null;
    this.selectedOperator = null;
    this.shouldResetDisplay = false;
    this.updateDisplay();
  }

  clearHistory() {
    this.history.clear();
  }

  useHistoryEntry(entryText) {
    const parts = entryText.split('=');
    if (parts.length === 2) {
      this.currentValue = parts[1].trim();
      this.shouldResetDisplay = true;
      this.updateDisplay();
    }
  }

  changeMode(mode, scientificRowSelector) {
    const scientificRow = document.querySelector(scientificRowSelector);
    scientificRow.style.display = mode === 'cientifica' ? 'grid' : 'none';
  }
}
