const DEGREES_TO_RADIANS = Math.PI / 180;
const RADIANS_TO_DEGREES = 180 / Math.PI;

function toRadians(value, angleUnit) {
  return angleUnit === 'DEG' ? value * DEGREES_TO_RADIANS : value;
}

function toAngleUnit(valueInRadians, angleUnit) {
  return angleUnit === 'DEG' ? valueInRadians * RADIANS_TO_DEGREES : valueInRadians;
}

export function applySine(value, angleUnit) {
  return Math.sin(toRadians(value, angleUnit));
}

export function applyCosine(value, angleUnit) {
  return Math.cos(toRadians(value, angleUnit));
}

export function applyTangent(value, angleUnit) {
  return Math.tan(toRadians(value, angleUnit));
}

export function applyArcsine(value, angleUnit) {
  return toAngleUnit(Math.asin(value), angleUnit);
}

export function applyArccosine(value, angleUnit) {
  return toAngleUnit(Math.acos(value), angleUnit);
}

export function applyArctangent(value, angleUnit) {
  return toAngleUnit(Math.atan(value), angleUnit);
}

export function applyLogarithm(value) {
  if (value <= 0) {
    throw new RangeError('El logaritmo requiere un número mayor a 0');
  }
  return Math.log10(value);
}

export function applyNaturalLog(value) {
  if (value <= 0) {
    throw new RangeError('El logaritmo natural requiere un número mayor a 0');
  }
  return Math.log(value);
}

export function applySquareRoot(value) {
  if (value < 0) {
    throw new RangeError('No existe raíz cuadrada real de un número negativo');
  }
  return Math.sqrt(value);
}

export function applySquare(value) {
  return value ** 2;
}

export function applyCube(value) {
  return value ** 3;
}

export function applyInverse(value) {
  if (value === 0) {
    throw new RangeError('No se puede dividir entre cero');
  }
  return 1 / value;
}

export function applySign(value) {
  return value * -1;
}
