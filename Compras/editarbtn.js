function editRow(btn) {
  const row = btn.closest('tr');
  const isEditing = btn.dataset.editing === 'true';

  if (!isEditing) {
    // Modo edición
    row.querySelectorAll('input, select').forEach(el => el.removeAttribute('readonly'));
    btn.textContent = 'Guardar';
    btn.dataset.editing = 'true';
    row.classList.add('editing');
  } else {

    // Modo guardar
    row.querySelectorAll('input, select').forEach(el => el.setAttribute('readonly', true));
    btn.textContent = 'Editar';
    btn.dataset.editing = 'false';
    row.classList.remove('editing');
  }
}  
