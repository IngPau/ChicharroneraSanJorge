document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".formulario");

  if (!form) return;

  form.addEventListener("submit", (e) => {
    const sucursal = form.id_sucursal.value;
    const numero = form.numero_mesa.value.trim();
    const capacidad = form.capacidad_mesa.value.trim();
    const estado = form.estado_mesa.value.trim();

    const enteroPositivo = /^\d+$/;

    // Validaciones básicas
    if (!sucursal || !numero || !estado) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Campos incompletos",
        text: "Sucursal, número de mesa y estado son obligatorios.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (!enteroPositivo.test(numero) || parseInt(numero, 10) <= 0) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "Número de mesa inválido",
        text: "Debe ser un entero positivo.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (
      capacidad &&
      (!enteroPositivo.test(capacidad) || parseInt(capacidad, 10) <= 0)
    ) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "Capacidad inválida",
        text: "Si la ingresas, debe ser un entero positivo.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }
  });
});
