<?php
// Ejemplo de datos (en la práctica los traerías de tu BD)
$vehiculosActivos = 8;
$mantenimientosPendientes = 2;
$vehiculosDisponibles = 5;
$vehiculosEnRuta = 3;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehículos - Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Chicharronera San Jorge</h2>
      <nav>
        <ul>
          <li><a href="index.php">Dashboard</a></li>
          <li><a href="ventas.php">Ventas</a></li>
          <li><a href="clientes.php">Clientes</a></li>
          <li><a href="proveedores.php">Proveedores</a></li>
          <li><a href="inventario.php">Inventario</a></li>
          <li><a href="planilla.php">Planilla</a></li>
          <li><a href="compras.php">Compras</a></li>
          <li><a href="servicio_domicilio.php">Servicio a Domicilio</a></li>
          <li><a href="sucursales.php">Sucursales</a></li>
          <li><a class="active" href="vehiculos.php">Vehículos</a></li>
          <li><a href="bi.php">Business Intelligence</a></li>
          <li><a href="reportes.php">Reportes</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main -->
    <main class="main">
      <header class="header">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <h1>Gestión de Vehículos</h1>
      </header>

      <!-- Cards resumen -->
      <section class="cards">
        <div class="card">
          <h3>Vehículos Activos</h3>
          <p class="number"><?php echo $vehiculosActivos; ?></p>
        </div>
        <div class="card">
          <h3>Mantenimientos Pendientes</h3>
          <p class="number"><?php echo $mantenimientosPendientes; ?></p>
        </div>
        <div class="card">
          <h3>Disponibles</h3>
          <p class="number"><?php echo $vehiculosDisponibles; ?></p>
        </div>
        <div class="card">
          <h3>En Ruta</h3>
          <p class="number"><?php echo $vehiculosEnRuta; ?></p>
        </div>
      </section>

       <!-- Sección de vehículos -->
    <section class="table-section">
      <div class="table-header">
        <h3>Listado de Vehículos</h3>
        <button class="btn-add" onclick="nuevoVehiculo()">Agregar Vehículo</button>
      </div>

      <table class="inventory-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Placa</th>
            <th>Modelo</th>
            <th>Estado</th>
            <th>Último Mantenimiento</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>P123BCD</td>
            <td>Toyota Hilux</td>
            <td><span class="success">Disponible</span></td>
            <td>2025-08-12</td>
            <td><button class="btn-delete" onclick="eliminarVehiculo(1)">🗑 Eliminar</button></td>
          </tr>
          <tr>
            <td>2</td>
            <td>P456EFG</td>
            <td>Nissan NP300</td>
            <td><span class="warning">En Ruta</span></td>
            <td>2025-07-30</td>
            <td><button class="btn-delete" onclick="eliminarVehiculo(2)">🗑 Eliminar</button></td>
          </tr>
          <tr>
            <td>3</td>
            <td>P789HIJ</td>
            <td>Mazda BT-50</td>
            <td><span class="danger">Mantenimiento</span></td>
            <td>2025-06-15</td>
            <td><button class="btn-delete" onclick="eliminarVehiculo(3)">🗑 Eliminar</button></td>
          </tr>
        </tbody>
      </table>
    </section>

    </main>
  </div>

  <script src="script.js"></script>
</body>
</html>
