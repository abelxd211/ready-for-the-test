const DARK_THEME_CLASS = 'tema-oscuro';

export class ThemeToggle {
  constructor(buttonId) {
    this.button = document.getElementById(buttonId);
    this.isDark = false;
  }

  init() {
    this.button.addEventListener('click', () => this.toggle());
  }

  toggle() {
    this.isDark = !this.isDark;
    document.body.classList.toggle(DARK_THEME_CLASS, this.isDark);
    this.button.textContent = this.isDark ? '🌙' : '☀️';
  }
}
