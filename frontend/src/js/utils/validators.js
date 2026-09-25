const MIN_PASSWORD_LENGTH = 8;
const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function isEmailValid(value) {
  return EMAIL_PATTERN.test(String(value).trim());
}

export function isPasswordValid(value) {
  return String(value).length >= MIN_PASSWORD_LENGTH;
}

export function isRequired(value) {
  return String(value ?? '').trim() !== '';
}

export function isInList(value, allowed) {
  return allowed.includes(value);
}

export function isPositiveNumber(value) {
  const number = Number(value);
  return Number.isFinite(number) && number > 0;
}

export function isInRange(value, min, max) {
  const number = Number(value);
  return Number.isFinite(number) && number >= min && number <= max;
}