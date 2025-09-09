// Configuración del gráfico de ventas semanales
const ctx = document.getElementById('salesChart').getContext('2d');

function toggleMenu() {
  document.querySelector('.sidebar').classList.toggle('show');
}


new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
    datasets: [{
      label: 'Ventas ($)',
      data: [4200, 3800, 5100, 4600, 6200, 7800, 7200],
      backgroundColor: 'rgba(239, 68, 68, 0.8)'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
