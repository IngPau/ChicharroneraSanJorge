document.addEventListener("DOMContentLoaded", () => {
  const msg = new URLSearchParams(window.location.search).get("msg");

  if (msg === "agregado") {
    Swal.fire({
      icon: "success",
      title: "Puesto agregado correctamente",
      showConfirmButton: false,
      timer: 1500,
    });
  }
  if (msg === "editado") {
    Swal.fire({
      icon: "success",
      title: "Puesto actualizado correctamente",
      showConfirmButton: false,
      timer: 1500,
    });
  }
  if (msg === "eliminado") {
    Swal.fire({
      icon: "info",
      title: "Puesto eliminado",
      showConfirmButton: false,
      timer: 1500,
    });
  }
  if (msg === "duplicado") {
    Swal.fire({
      icon: "error",
      title: "Puesto duplicado",
      text: "Ya existe un puesto con ese nombre.",
      confirmButtonColor: "#dc2626",
    });
  }

  const botonesEliminar = document.querySelectorAll(".btn-eliminar");
  botonesEliminar.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const link = btn.getAttribute("href");
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Esto eliminará el puesto permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
      }).then((r) => {
        if (r.isConfirmed) window.location.href = link;
      });
    });
  });
});
