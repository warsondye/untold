
  if (JSON.parse(localStorage.getItem('darkMode'))) {
    document.addEventListener("DOMContentLoaded", () => {
      document.body.classList.add('dark-mode');
    });
  }

