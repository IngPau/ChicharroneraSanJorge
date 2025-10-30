document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".formulario");

  form.addEventListener("submit", (e) => {
    const nombre = form.nombre_puestos.value.trim();
    const salario = form.salario_base_puestos.value.trim();

    if (!nombre || !salario) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Campos incompletos",
        text: "Por favor completa todos los campos obligatorios.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (isNaN(salario) || parseFloat(salario) <= 0) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "Salario inválido",
        text: "El salario base debe ser un número positivo.",
        confirmButtonColor: "#dc2626",
      });
    }
  });
});
