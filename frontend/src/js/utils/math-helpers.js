const DECIMAL_PLACES = 10;
const ROUNDING_FACTOR = 10 ** DECIMAL_PLACES;

export function roundResult(value) {
  return Math.round(value * ROUNDING_FACTOR) / ROUNDING_FACTOR;
}

export function calculateFactorial(number) {
  if (number < 0 || !Number.isInteger(number)) {
    return null;
  }
  let result = 1;
  for (let i = 2; i <= number; i++) {
    result *= i;
  }
  return result;
}
