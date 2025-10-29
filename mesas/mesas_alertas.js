document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");

  // ======================= ALERTAS DE RESULTADOS =======================
  if (msg === "agregado") {
    Swal.fire({
      icon: "success",
      title: "Mesa agregada correctamente",
      showConfirmButton: false,
      timer: 1500,
    });
  }

  if (msg === "editado") {
    Swal.fire({
      icon: "success",
      title: "Mesa actualizada correctamente",
      showConfirmButton: false,
      timer: 1500,
    });
  }

  if (msg === "eliminado") {
    Swal.fire({
      icon: "info",
      title: "Mesa eliminada",
      showConfirmButton: false,
      timer: 1500,
    });
  }

  if (msg === "duplicado") {
    Swal.fire({
      icon: "error",
      title: "Datos duplicados",
      text: "Ya existe una mesa con el mismo número en esa sucursal.",
      confirmButtonColor: "#dc2626",
    });
  }

  // ======================= CONFIRMACIÓN ANTES DE ELIMINAR =======================
  const botonesEliminar = document.querySelectorAll(".btn-eliminar");

  botonesEliminar.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault(); // Evita que se ejecute el enlace directamente

      const enlace = btn.getAttribute("href");

      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará la mesa permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = enlace;
        }
      });
    });
  });
});
