// Validación sencilla de formularios y helper para confirmar borrados
function confirmDelete(message) {
  return confirm(message || '¿Eliminar el registro?');
}

// Auto-enfoque al primer input
window.addEventListener('DOMContentLoaded', () => {
  const firstInput = document.querySelector('form input, form select, form textarea');
  if (firstInput) firstInput.focus();
});
