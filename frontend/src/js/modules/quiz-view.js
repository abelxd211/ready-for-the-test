import { clearChildren } from '../utils/dom-helpers.js';
import { session } from './session.js';
import { renderQuizTeacher } from './quiz-teacher-view.js';
import { renderQuizStudent } from './quiz-student-view.js';

export function renderQuiz(container) {
  clearChildren(container);
  if (session.isTeacher) {
    renderQuizTeacher(container);
  } else {
    renderQuizStudent(container);
  }
}