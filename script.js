/* ===================== TOGGLE MENU ===================== */
function toggleMenu() {
  document.querySelector('.sidebar').classList.toggle('show');
}

/* ===================== SWEETALERT - NUEVO PRODUCTO ===================== */
function nuevoProducto() {
  Swal.fire({
    title: 'Agregar producto',
    html: `
      <input type="text" id="nombre" class="swal2-input" placeholder="Nombre">
      <input type="text" id="categoria" class="swal2-input" placeholder="Categoría">
      <input type="number" id="cantidad" class="swal2-input" placeholder="Cantidad">
      <input type="number" id="precio" class="swal2-input" placeholder="Precio (Q)">
    `,
    confirmButtonText: 'Guardar',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const nombre = document.getElementById('nombre').value;
      const categoria = document.getElementById('categoria').value;
      const cantidad = document.getElementById('cantidad').value;
      const precio = document.getElementById('precio').value;

      if (!nombre || !categoria || !cantidad || !precio) {
        Swal.showValidationMessage('⚠️ Todos los campos son obligatorios');
      }

      return { nombre, categoria, cantidad, precio };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire('Guardado ✅', 'Producto agregado correctamente', 'success');

    }
  });
}

/* ===================== CHART - VENTAS SEMANALES ===================== */
document.addEventListener("DOMContentLoaded", () => {
  const ctx = document.getElementById('salesChart');
  if (ctx) {
    new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        datasets: [{
          label: 'Ventas (Q)',
          data: [4200, 3800, 5100, 4600, 6200, 7800, 7200],
          backgroundColor: 'rgba(239, 68, 68, 0.8)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }

  
});

function nuevoVehiculo() {
  Swal.fire({
    title: 'Agregar Vehículo',
    html: `
      <input type="text" id="placa" class="swal2-input" placeholder="Placa">
      <input type="text" id="modelo" class="swal2-input" placeholder="Modelo">
      <select id="estado" class="swal2-input">
        <option value="Disponible">Disponible</option>
        <option value="En Ruta">En Ruta</option>
        <option value="Mantenimiento">Mantenimiento</option>
      </select>
      <input type="date" id="mantenimiento" class="swal2-input">
    `,
    confirmButtonText: 'Guardar',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const placa = document.getElementById('placa').value;
      const modelo = document.getElementById('modelo').value;
      const estado = document.getElementById('estado').value;
      const mantenimiento = document.getElementById('mantenimiento').value;

      if (!placa || !modelo || !estado || !mantenimiento) {
        Swal.showValidationMessage('⚠️ Todos los campos son obligatorios');
      }
      return { placa, modelo, estado, mantenimiento };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire('✅ Guardado', 'Vehículo agregado correctamente', 'success');

    }
  });
}

function eliminarVehiculo(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Este vehículo será eliminado",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire('🗑 Eliminado', 'El vehículo fue eliminado', 'success');

    }
  });
}

