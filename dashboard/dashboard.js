document.addEventListener("DOMContentLoaded", () => {
  // === Gráfica: Ventas últimos 7 días ===
  const ctx1 = document.getElementById("chartVentas");
  new Chart(ctx1, {
    type: "line",
    data: {
      labels: ventasLabels,
      datasets: [
        {
          label: "Ventas (Q)",
          data: ventasData,
          borderWidth: 3,
          fill: true,
          borderColor: "#dc2626",
          backgroundColor: "rgba(220, 38, 38, 0.15)",
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
    },
  });

  // === Gráfica: Top 5 Platos ===
  const ctx2 = document.getElementById("chartTopPlatos");
  new Chart(ctx2, {
    type: "bar",
    data: {
      labels: topLabels,
      datasets: [
        {
          label: "Cantidad Vendida",
          data: topCant,
          backgroundColor: "#374151",
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
    },
  });
});
