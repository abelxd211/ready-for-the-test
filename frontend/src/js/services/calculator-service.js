import { request } from './api.js';

export function createOperation({ expression, result, operationType }) {
  return request('/api/calculator-operations', {
    method: 'POST',
    body: {
      expression,
      result,
      operation_type: operationType,
    },
  });
}

export function listOperations() {
  return request('/api/calculator-operations');
}