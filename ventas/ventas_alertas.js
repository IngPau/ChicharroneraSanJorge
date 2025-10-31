// ✅ Venta agregada
if (window.location.search.includes("agregado=1")) {
  Swal.fire({
    icon: 'success',
    title: 'Venta registrada',
    text: 'La venta se agregó correctamente.',
    confirmButtonColor: '#1e293b'
  });
}

// ✅ Venta editada
if (window.location.search.includes("editado=1")) {
  Swal.fire({
    icon: 'success',
    title: 'Venta actualizada',
    text: 'Los datos de la venta fueron modificados.',
    confirmButtonColor: '#1e293b'
  });
}

// ✅ Venta eliminada
if (window.location.search.includes("eliminado=1")) {
  Swal.fire({
    icon: 'success',
    title: 'Venta eliminada',
    text: 'La venta fue eliminada correctamente.',
    confirmButtonColor: '#1e293b'
  });
}

// ⚠️ Nueva alerta: sin suficiente stock
if (window.location.search.includes("sin_stock=1")) {
  Swal.fire({
    icon: 'error',
    title: 'Stock insuficiente',
    text: 'No hay suficiente materia prima en inventario para completar esta venta.',
    confirmButtonColor: '#dc2626'
  });
}

// 🚫 Confirmación antes de eliminar
document.querySelectorAll(".btn-eliminar").forEach(btn => {
  btn.addEventListener("click", function(e) {
    e.preventDefault();
    const url = this.getAttribute("href");

    Swal.fire({
      title: '¿Eliminar venta?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
    });
  });
});

