import { renderShell } from './modules/app-shell.js';
import { session } from './modules/session.js';
import { startRouter } from './modules/router.js';
import { initAssistant } from './modules/assistant-view.js';

async function boot() {
  renderShell();
  await session.init();
  startRouter();
  initAssistant();
}

boot();