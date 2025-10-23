document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");

  if (msg === "agregado") {
    Swal.fire({
      icon: "success",
      title: "Empleado agregado correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "editado") {
    Swal.fire({
      icon: "success",
      title: "Empleado actualizado correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "eliminado") {
    Swal.fire({
      icon: "info",
      title: "Empleado eliminado",
      showConfirmButton: false,
      timer: 1500
    });
  }
});
