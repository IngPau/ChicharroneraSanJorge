<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Clientes</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Chicharonera San Jorge</h2>
      <nav>
        <ul>
          <li><a href="index.php">Dashboard</a></li>     
          <li><a href="ventas.php">Ventas</a></li>     
          <li class="active"><a href="clientes.php">Clientes</a></li>
          <li><a href="proveedores.php">Proveedores</a></li>
          <li><a href="inventario.php">Inventario</a></li>
          <li><a href="planilla.php">Planilla</a></li>
          <li><a href="compras.php">Compras</a></li>
          <li><a href="servicio_domicilio.php">Servicio a Domicilio</a></li>
          <li><a href="sucursales.php">Sucursales</a></li>
          <li><a href="vehiculos.php">Vehículos</a></li>
          <li><a href="bi.php">Business Intelligence</a></li>
          <li><a href="reportes.php">Reportes</a></li>
        </ul>
      </nav>
    </aside>

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




