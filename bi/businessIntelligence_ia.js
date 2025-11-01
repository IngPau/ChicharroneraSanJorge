document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("btnConsultar");
  const preguntaInput = document.getElementById("pregunta");
  const respuestaIA = document.getElementById("respuestaIA");
  const tituloGrafico = document.getElementById("tituloGrafico");
  const contenedorGrafico = document.querySelector(".resultado"); // 👈 el contenedor del canvas
  let chartInstance = null;

  btn.addEventListener("click", async () => {
    const query = preguntaInput.value.trim();

    if (!query) {
      Swal.fire({
        icon: "warning",
        title: "Campo vacío",
        text: "Por favor, ingresa una pregunta o consulta.",
        confirmButtonColor: "#dc2626",
      });
      return;
    }

    btn.disabled = true; // Evita doble clic

    Swal.fire({
      title: "Procesando...",
      text: "Analizando tu consulta con la IA...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      const response = await fetch("businessIntelligence_backend.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ pregunta: query }),
      });

      const rawText = await response.text();
      console.log("Respuesta cruda:", rawText);

      let data;
      try {
        data = JSON.parse(rawText);
      } catch (e) {
        console.error("Error al parsear JSON:", rawText);
        throw new Error("El backend no devolvió JSON válido.");
      }

      Swal.close();

      if (data.error) throw new Error(data.error);
      if (!data.chartData || !data.chartData.labels.length) {
        Swal.fire({
          icon: "info",
          title: "Sin datos",
          text: "No se encontraron resultados para la consulta.",
        });
        return;
      }

      // 🔄 Destruir gráfico anterior si existe
      if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
      }

      // 🧹 Eliminar el canvas anterior completamente del DOM
      const oldCanvas = document.getElementById("grafico");
      if (oldCanvas) oldCanvas.remove();

      // Crear un nuevo canvas limpio
      const newCanvas = document.createElement("canvas");
      newCanvas.id = "grafico";
      contenedorGrafico.appendChild(newCanvas);

      //Pequeño delay para asegurar render limpio
      await new Promise((res) => setTimeout(res, 50));

      //Mostrar el título dinámico generado por la IA
      tituloGrafico.textContent = data.titulo || "Visualización de Datos";

      //Crear el nuevo gráfico sin conflictos
      const maxValue = Math.max(...data.chartData.datasets[0].data);
      const suggestedMax = Math.ceil(maxValue * 1.1);

      chartInstance = new Chart(newCanvas, {
        type: data.chartType || "bar",
        data: data.chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              suggestedMax: suggestedMax,
            },
          },
          plugins: {
            legend: { display: true, position: "top" },
          },
        },
      });

      respuestaIA.textContent =
        data.interpretacion ||
        "No se pudo generar una interpretación de los datos.";

      Swal.fire({
        icon: "success",
        title: "Consulta completada",
        text: "Los datos se procesaron correctamente.",
        timer: 1800,
        showConfirmButton: false,
      });
    } catch (error) {
      console.error("Error capturado:", error);
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error en la consulta",
        text:
          "Ocurrió un problema al procesar la información.\nDetalles: " +
          error.message,
        confirmButtonColor: "#dc2626",
      });
    } finally {
      btn.disabled = false;
    }
  });
});
