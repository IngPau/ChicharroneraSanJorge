document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formVehiculo');

  // 🚗 Envío del formulario (agregar / editar)
  if (form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      // Detectar si es agregar o editar
      if (form.querySelector('[name="editar"]')) {
        formData.append('editar', '1');
      } else {
        formData.append('agregar', '1');
      }

      try {
        const response = await fetch('CRUD_Vehiculos.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: result.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            window.location.href = 'vehiculos.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message
          });
        }
      } catch (error) {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error de conexión',
          text: 'No se pudo comunicar con el servidor.'
        });
      }
    });
  }

  // 🗑️ Confirmación y eliminación por AJAX
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', async e => {
      e.preventDefault();
      const url = btn.getAttribute('href');

      const confirm = await Swal.fire({
        title: '¿Eliminar vehículo?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (confirm.isConfirmed) {
        try {
          const res = await fetch(url);
          const data = await res.json();

          Swal.fire({
            icon: data.status === 'success' ? 'success' : 'error',
            title: data.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            window.location.reload();
          });
        } catch (error) {
          console.error('Error al eliminar:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo completar la eliminación.'
          });
        }
      }
    });
  });
});
