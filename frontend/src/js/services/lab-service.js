import { request } from './api.js';

export function createSample({ name, mass, volume, state }) {
  return request('/api/lab-samples', {
    method: 'POST',
    body: { name, mass, volume, state },
  });
}

export function listSamples() {
  return request('/api/lab-samples');
}

export function deleteSample(sampleId) {
  return request(`/api/lab-samples/${sampleId}`, { method: 'DELETE' });
}

export function listKnownSubstances() {
  return request('/api/known-substances');
}

export function listReagents() {
  return request('/api/reagents');
}

export function listReactions() {
  return request('/api/mixture-reactions');
}

export function mixReagents({ reagentAId, reagentBId }) {
  return request('/api/mixing', {
    method: 'POST',
    body: { reagent_a_id: reagentAId, reagent_b_id: reagentBId },
  });
}

export function identifySample({ state, transparency, conductivity, density }) {
  return request('/api/identify', {
    method: 'POST',
    body: { state, transparency, conductivity, density },
  });
}

export function listExperiments() {
  return request('/api/lab-experiments');
}