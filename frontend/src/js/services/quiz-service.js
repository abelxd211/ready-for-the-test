import { request } from './api.js';

export function createRoom({ title, questions }) {
  return request('/api/quiz-rooms', {
    method: 'POST',
    body: { title, questions },
  });
}

export function listRooms() {
  return request('/api/quiz-rooms');
}

export function getRoom(roomId) {
  return request(`/api/quiz-rooms/${roomId}`);
}

export function deleteRoom(roomId) {
  return request(`/api/quiz-rooms/${roomId}`, { method: 'DELETE' });
}

export function joinRoomWithCode(code) {
  return request('/api/quiz-rooms/join', {
    method: 'POST',
    body: { code },
  });
}

export function autoRoomTopics() {
  return request('/api/quiz-rooms/auto/topics');
}

export function createAutoRoom({ title, topic, questionCount, pointsValue }) {
  return request('/api/quiz-rooms/auto', {
    method: 'POST',
    body: {
      title,
      topic,
      question_count: questionCount,
      points_value: pointsValue,
    },
  });
}

export function submitAnswers({ attemptId, answers }) {
  return request('/api/quiz-answers', {
    method: 'POST',
    body: { attempt_id: attemptId, answers },
  });
}