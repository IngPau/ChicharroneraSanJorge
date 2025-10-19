<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Clientes</title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include '../SideBar/sidebar.php'; ?>

    <!-- Main -->
    <main class="main">
      <!-- Encabezado -->
      <div class="header">
        <h1>Gestión de Clientes</h1>
        <button class="btn-nuevo">+ Nuevo Cliente</button>
      </div>

      <!-- Tarjetas de resumen -->
      <div class="cards">
        <div class="card">
          <p>Total Clientes</p>
          <p class="number">100</p>
        </div>
        <div class="card">
          <p>Nuevos (30 días)</p>
          <p class="number">20</p>
        </div>
      </div>

      <!-- Lista de clientes -->
      <div class="clientes-grid">
        <div class="cliente-card">
          <h3>María Morales</h3>
          <p><b>Email:</b> Maria.Morales@gmail.com</p>
          <p><b>Tel:</b> +502 4059 7362</p>
          <p><b>Dir:</b> Calle 24, Zona 7, Ciudad de Guatemala</p>
        </div>

        <div class="cliente-card">
          <h3>Kevin Natareno</h3>
          <p><b>Email:</b> Kevin.Natareno@gmail.com</p>
          <p><b>Tel:</b> +502 5837 9683</p>
          <p><b>Dir:</b> Calle 12, Zona 7, Ciudad de Guatemala</p>
        </div>

        <div class="cliente-card">
          <h3>Paula Leonardo</h3>
          <p><b>Email:</b> Paula.Leonardo@gmail.com</p>
          <p><b>Tel:</b> +502 3062 3363</p>
          <p><b>Dir:</b> Calle 24, Zona 11, Ciudad de Guatemala</p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>




