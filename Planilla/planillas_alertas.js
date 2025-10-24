document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const mensaje = urlParams.get("mensaje");

  // ✅ Mensajes automáticos de alerta (según la acción)
  if (mensaje === "agregado") {
    Swal.fire({
      icon: "success",
      title: "¡Nómina registrada correctamente!",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (mensaje === "editado") {
    Swal.fire({
      icon: "success",
      title: "¡Nómina actualizada correctamente!",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (mensaje === "eliminado") {
    Swal.fire({
      icon: "info",
      title: "Nómina eliminada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  // ✅ Confirmación antes de eliminar
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const href = this.href;
      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará la nómina permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = href; // Redirige para ejecutar la eliminación en el servidor
        }
      });
    });
  });
});
