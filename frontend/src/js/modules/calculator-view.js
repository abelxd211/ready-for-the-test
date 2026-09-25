import { clearChildren } from '../utils/dom-helpers.js';
import { showToast } from '../utils/toast.js';
import { Calculator } from './calculator.js';
import * as calculatorService from '../services/calculator-service.js';

const CALCULATOR_HTML = `
  <section class="calculadora-cajon">
    <div class="panel-superior">
      <select id="selectorModo" aria-label="Modo de la calculadora">
        <option value="basica">Básica</option>
        <option value="cientifica" selected>Científica</option>
      </select>
      <div class="panel-superior-acciones">
        <span id="indicadorAngulo">RAD</span>
        <button data-action="toggle-angle">RAD/DEG</button>
      </div>
    </div>

    <input type="text" id="pantalla" value="0" disabled aria-label="Pantalla">

    <div class="botones botones-cientificas">
      <button data-action="function" data-value="sin">sin</button>
      <button data-action="function" data-value="cos">cos</button>
      <button data-action="function" data-value="tan">tan</button>
      <button data-action="function" data-value="log">log</button>
      <button data-action="function" data-value="asin">sin⁻¹</button>
      <button data-action="function" data-value="acos">cos⁻¹</button>
      <button data-action="function" data-value="atan">tan⁻¹</button>
      <button data-action="function" data-value="ln">ln</button>
      <button data-action="function" data-value="sqrt">√</button>
      <button data-action="function" data-value="cuadrado">x²</button>
      <button data-action="function" data-value="cubo">x³</button>
      <button data-action="operator" data-value="^">xʸ</button>
      <button data-action="function" data-value="factorial">n!</button>
      <button data-action="function" data-value="inverso">1/x</button>
      <button data-action="memory-add">M+</button>
      <button data-action="memory-recall">MR</button>
      <button data-action="memory-clear">MC</button>
      <button data-action="operator" data-value="%">%</button>
      <button data-action="function" data-value="signo">±</button>
    </div>

    <div class="botones">
      <button class="limpiar" data-action="clear">C</button>
      <button class="borrar" data-action="delete">⌫</button>
      <button data-action="parenthesis" data-value="(">(</button>
      <button data-action="parenthesis" data-value=")">)</button>

      <button data-action="number" data-value="7">7</button>
      <button data-action="number" data-value="8">8</button>
      <button data-action="number" data-value="9">9</button>
      <button class="operador" data-action="operator" data-value="/">÷</button>

      <button data-action="number" data-value="4">4</button>
      <button data-action="number" data-value="5">5</button>
      <button data-action="number" data-value="6">6</button>
      <button class="operador" data-action="operator" data-value="*">×</button>

      <button data-action="number" data-value="1">1</button>
      <button data-action="number" data-value="2">2</button>
      <button data-action="number" data-value="3">3</button>
      <button class="operador" data-action="operator" data-value="-">−</button>

      <button data-action="number" data-value="0">0</button>
      <button data-action="decimal">.</button>
      <button class="igual" data-action="equals">=</button>
      <button class="operador" data-action="operator" data-value="+">+</button>
    </div>

    <div class="panel-historial">
      <div class="titulo-historial">
        <span>Historial</span>
        <button data-action="clear-history">Borrar</button>
      </div>
      <ul id="listaHistorial" class="lista-historial"></ul>
    </div>
  </section>
`;

export function renderCalculator(container) {
  clearChildren(container);
  const section = document.createElement('section');
  section.classList.add('vista-cabecera');
  section.appendChild(elHeading());
  container.appendChild(section);

  const wrapper = document.createElement('div');
  wrapper.innerHTML = CALCULATOR_HTML.trim();
  container.appendChild(wrapper.firstElementChild);

  const calculator = new Calculator('pantalla', 'listaHistorial', persistOperation, (message) => showToast(message, 'error'));
  wireActions(calculator, container);
  calculator.updateDisplay();
  seedHistory(calculator);
}

function elHeading() {
  const heading = document.createElement('div');
  const title = document.createElement('h2');
  title.textContent = 'Calculadora científica';
  const subtitle = document.createElement('p');
  subtitle.textContent = 'Operaciones básicas y científicas. Cada cálculo queda guardado en tu historial.';
  heading.appendChild(title);
  heading.appendChild(subtitle);
  return heading;
}

function wireActions(calculator, container) {
  container.querySelectorAll('[data-action="number"]').forEach((button) => {
    button.addEventListener('click', () => calculator.pressNumber(button.dataset.value));
  });
  container.querySelectorAll('[data-action="operator"]').forEach((button) => {
    button.addEventListener('click', () => calculator.pressOperator(button.dataset.value));
  });
  container.querySelectorAll('[data-action="function"]').forEach((button) => {
    button.addEventListener('click', () => calculator.applyFunction(button.dataset.value));
  });
  container.querySelectorAll('[data-action="parenthesis"]').forEach((button) => {
    button.addEventListener('click', () => calculator.pressParenthesis(button.dataset.value));
  });

  container.querySelector('[data-action="clear"]').addEventListener('click', () => calculator.clearAll());
  container.querySelector('[data-action="delete"]').addEventListener('click', () => calculator.deleteLastDigit());
  container.querySelector('[data-action="equals"]').addEventListener('click', () => calculator.calculateResult());
  container.querySelector('[data-action="decimal"]').addEventListener('click', () => calculator.pressDecimalPoint());
  container.querySelector('[data-action="memory-add"]').addEventListener('click', () => calculator.saveToMemory());
  container.querySelector('[data-action="memory-recall"]').addEventListener('click', () => calculator.recallMemory());
  container.querySelector('[data-action="memory-clear"]').addEventListener('click', () => calculator.clearMemory());
  container.querySelector('[data-action="clear-history"]').addEventListener('click', () => calculator.clearHistory());
  container.querySelector('[data-action="toggle-angle"]').addEventListener('click', () => calculator.toggleAngleUnit('indicadorAngulo'));
  container.querySelector('#selectorModo').addEventListener('change', (event) => {
    calculator.changeMode(event.target.value, '.botones-cientificas');
  });
}

function persistOperation({ expression, result, operationType }) {
  calculatorService.createOperation({ expression, result, operationType }).catch(() => {});
}

function seedHistory(calculator) {
  calculatorService.listOperations()
    .then((operations) => {
      const entries = operations.map((operation) => ({
        text: `${operation.expression} = ${operation.result}`,
      }));
      calculator.seedHistoryFromServer(entries);
    })
    .catch(() => {});
}