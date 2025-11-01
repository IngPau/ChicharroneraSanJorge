document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");

  // ================= ALERTAS EMPLEADOS =================
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

  // ================= ALERTAS ASISTENCIA =================
  if (msg === "agregarAsistencia") {
    Swal.fire({
      icon: "success",
      title: "Asistencia registrada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "editarAsistencia") {
    Swal.fire({
      icon: "success",
      title: "Asistencia actualizada correctamente",
      showConfirmButton: false,
      timer: 1500
    });
  }

  if (msg === "eliminar") {
    Swal.fire({
      icon: "info",
      title: "Asistencia eliminada",
      showConfirmButton: false,
      timer: 1500
    });
  }

  // ================= FORMULARIO NÓMINA =================
  const btnNomina = document.getElementById('btnNomina');
  const formNomina = document.getElementById('formNomina');
  const cerrarNomina = document.getElementById('cerrarNomina');

  if (urlParams.get("editarNomina")) {
    formNomina.style.display = 'grid';
    if (btnNomina) btnNomina.style.display = 'none';
  }

  btnNomina?.addEventListener('click', () => {
    formNomina.style.display = 'grid';
    btnNomina.style.display = 'none';
  });

  cerrarNomina?.addEventListener('click', () => {
    formNomina.style.display = 'none';
    if (btnNomina) btnNomina.style.display = 'inline-flex';
  });

  // ================= FORMULARIO ASISTENCIA =================
  const btnAsistencia = document.getElementById('btnAsistencia');
  const formAsistencia = document.getElementById('formAsistencia');
  const cerrarAsistencia = document.getElementById('cerrarAsistencia');

  // Mostrar formulario automáticamente si se está editando una asistencia
  if (urlParams.get("editarAsistencia")) {
    formAsistencia.style.display = 'grid';
    if (btnAsistencia) btnAsistencia.style.display = 'none';
  }

  btnAsistencia?.addEventListener('click', () => {
    formAsistencia.style.display = 'grid';
    btnAsistencia.style.display = 'none';
  });

  cerrarAsistencia?.addEventListener('click', () => {
    formAsistencia.style.display = 'none';
    if (btnAsistencia) btnAsistencia.style.display = 'inline-flex';
  });
});
