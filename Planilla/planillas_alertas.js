document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");

  // ================= ALERTAS NÓMINA =================
  if (msg === "nomina_agregada") {
    Swal.fire({
      icon: "success",
      title: "Nómina agregada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "nomina_editada") {
    Swal.fire({
      icon: "success",
      title: "Nómina actualizada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "nomina_eliminada") {
    Swal.fire({
      icon: "info",
      title: "Nómina eliminada",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "error") {
    Swal.fire({
      icon: "error",
      title: "Ocurrió un error al procesar la nómina",
      showConfirmButton: false,
      timer: 1800
    });
  }
});
