<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo de Inteligencia de Negocio | Chicharronera San Jorge</title>
  <link rel="stylesheet" href="../globales.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="businessIntelligence_ia.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Bsiness Intelligence</h1>
      <h3>Asistente Analítico</h3>

      <!-- Bloque 1: Entrada de consulta -->
      <section class="consulta">
        <label for="pregunta">Haz una pregunta sobre los datos:</label>
        <div class="input-area">
          <input type="text" id="pregunta" placeholder="Ej: mostrar vehiculos disponibles y en ruta...">
          <button id="btnConsultar">Consultar</button>
        </div>
      </section>

      <!-- Bloque 2: Visualización de gráfico -->
      <section class="resultado">
        <h3 id="tituloGrafico" class="text-lg font-semibold text-center mb-3">Visualización de Datos</h3>
        <canvas id="grafico"></canvas>
      </section>

      <!-- Bloque 3: Interpretación -->
      <section class="interpretacion">
        <h3>Interpretación</h3>
        <p id="respuestaIA"></p>
      </section>
    </main>
  </div>

  <script src="businessIntelligence_ia.js"></script>
</body>
</html>
