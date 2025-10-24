document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");

  if (msg === "agregado") {
    Swal.fire({
      icon: "success",
      title: "Sucursal agregada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "editado") {
    Swal.fire({
      icon: "success",
      title: "Sucursal actualizada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "eliminado") {
    Swal.fire({
      icon: "info",
      title: "Sucursal eliminada",
      showConfirmButton: false,
      timer: 1500
    });
  }

  // Confirmación antes de eliminar: intercepta los enlaces con clase .btn-eliminar
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const href = this.href;
      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará la sucursal permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // Si confirma, navegar al enlace que ejecuta la eliminación en el servidor
          window.location.href = href;
        }
      });
    });
  });
});
