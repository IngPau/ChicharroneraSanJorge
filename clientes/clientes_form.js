document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".formulario");

  form.addEventListener("submit", (e) => {
    const nombre = form.nombre_cliente.value.trim();
    const apellido = form.apellido_cliente.value.trim();
    const dpi = form.dpi_cliente.value.trim();
    const telefono = form.telefono_cliente.value.trim();
    const direccion = form.direccion_cliente.value.trim();
    const correo = form.correo_cliente.value.trim();

    // Expresiones regulares
    const regexDPI = /^\d{13}$/;
    const regexTelefono = /^\d{8,20}$/;
    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Validaciones básicas
    if (!nombre || !apellido || !dpi || !telefono || !direccion || !correo) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Campos incompletos",
        text: "Por favor completa todos los campos antes de continuar.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (!regexDPI.test(dpi)) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "DPI inválido",
        text: "El DPI debe tener exactamente 13 dígitos numéricos.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (!regexTelefono.test(telefono)) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "Teléfono inválido",
        text: "El número de teléfono debe tener entre 8 y 10 dígitos numéricos.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    if (!regexCorreo.test(correo)) {
      e.preventDefault();
      Swal.fire({
        icon: "error",
        title: "Correo inválido",
        text: "Por favor ingresa un correo electrónico válido.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }
  });
});
