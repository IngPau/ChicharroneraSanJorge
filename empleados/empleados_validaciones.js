document.addEventListener("DOMContentLoaded", () => {
  // ================= VALIDACIÓN DE NOMBRE =================
  const nombreInput = document.querySelector("input[name='nombre_empleados']");
  if (nombreInput) {
    nombreInput.addEventListener("input", () => {
      // Solo letras y espacios
      nombreInput.value = nombreInput.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ\s]/g, "");

      if (nombreInput.value.length > 0 && !/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/.test(nombreInput.value)) {
        Swal.fire({
          icon: "warning",
          title: "Nombre inválido",
          text: "El nombre solo puede contener letras y espacios.",
          timer: 1500,
          showConfirmButton: false
        });
      }
    });
  }

  // ================= VALIDACIÓN DE APELLIDO =================
  const apellidoInput = document.querySelector("input[name='apellido_empleados']");
  if (apellidoInput) {
    apellidoInput.addEventListener("input", () => {
      apellidoInput.value = apellidoInput.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúñÑ\s]/g, "");

      if (apellidoInput.value.length > 0 && !/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/.test(apellidoInput.value)) {
        Swal.fire({
          icon: "warning",
          title: "Apellido inválido",
          text: "El apellido solo puede contener letras y espacios.",
          timer: 1500,
          showConfirmButton: false
        });
      }
    });
  }

  // ================= VALIDACIÓN DE DPI =================
  const dpiInput = document.querySelector("input[name='dpi_empleados']");
  if (dpiInput) {
    dpiInput.addEventListener("input", () => {
      // Permitir solo números
      dpiInput.value = dpiInput.value.replace(/\D/g, "");

      if (dpiInput.value.length > 13) {
        dpiInput.value = dpiInput.value.slice(0, 13);
        Swal.fire({
          icon: "warning",
          title: "DPI demasiado largo",
          text: "El DPI debe tener exactamente 13 dígitos.",
          timer: 1500,
          showConfirmButton: false
        });
      }
    });
  }

  // ================= VALIDACIÓN DE TELÉFONO =================
  const telefonoInput = document.querySelector("input[name='telefono_empleados']");
  if (telefonoInput) {
    telefonoInput.addEventListener("input", () => {
      // Permitir solo números
      telefonoInput.value = telefonoInput.value.replace(/\D/g, "");

      // Limitar a 8 dígitos (formato típico guatemalteco)
      if (telefonoInput.value.length > 8) {
        telefonoInput.value = telefonoInput.value.slice(0, 8);
        Swal.fire({
          icon: "warning",
          title: "Teléfono demasiado largo",
          text: "El número debe tener 8 dígitos.",
          timer: 1500,
          showConfirmButton: false
        });
      }
    });
  }

  // ================= VALIDACIÓN FINAL AL ENVIAR FORMULARIO =================
  const form = document.querySelector("form[action='empleados_crud.php']");
  if (form) {
    form.addEventListener("submit", (e) => {
      const nombre = nombreInput?.value.trim() || "";
      const apellido = apellidoInput?.value.trim() || "";
      const dpi = dpiInput?.value.trim() || "";
      const telefono = telefonoInput?.value.trim() || "";

      if (!/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/.test(nombre)) {
        e.preventDefault();
        Swal.fire({
          icon: "error",
          title: "Error en el nombre",
          text: "El nombre solo puede contener letras y espacios.",
        });
        return;
      }

      if (!/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/.test(apellido)) {
        e.preventDefault();
        Swal.fire({
          icon: "error",
          title: "Error en el apellido",
          text: "El apellido solo puede contener letras y espacios.",
        });
        return;
      }

      if (!/^\d{13}$/.test(dpi)) {
        e.preventDefault();
        Swal.fire({
          icon: "error",
          title: "Error en el DPI",
          text: "El DPI debe tener exactamente 13 dígitos numéricos.",
        });
        return;
      }

      if (!/^\d{8}$/.test(telefono)) {
        e.preventDefault();
        Swal.fire({
          icon: "error",
          title: "Error en el teléfono",
          text: "El teléfono debe tener exactamente 8 dígitos numéricos.",
        });
        return;
      }
    });
  }
});
