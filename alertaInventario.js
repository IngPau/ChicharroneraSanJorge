document.addEventListener('DOMContentLoaded', function() {
  Alerta();
});

function Alerta() {
  fetch("consultaStock.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.estado == 1) {
        Swal.fire({
          title: "Stock Bajo",
          text: "Hay materias primas con stock bajo.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ver Inventario",
          cancelButtonText: "Continuar",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "inventario/materiaPrima/inventarioMp.php";
          }
        });
      }
    });
}
