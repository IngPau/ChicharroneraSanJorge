<?php
session_start(); // Iniciar la sesión
//Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
  header("Location: ../login/login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pago Proveedores</title>
  <link rel="stylesheet" href="proveedores.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include '../SideBar/sidebar.php'; ?>

 <div class="main">
    <header>
      <h1>Ingreso de Pago a Proveedores</h1>
    </header>

    <section class="card">
      <h2>Agregar Pago a Proveedor</h2>
      <form name="supplierForm" action="ingresopago.php" method="GET">
        <div class="form-group">
          <label for="compra">ID de la Compra:</label>
          <input type="text" id="compra" name="compra" required>
        </div>
        <div class="form-group">
          <label for="pago">Fecha del pago a proveedores:</label>
          <input type="date" id="pago" name="pago">
        </div>
        <div class="form-group">
          <label for="monto">Monto:</label>
          <input type="tel" id="monto" name="monto">
        </div>
        <button type="button" class="btn" id="btn" onclick="guardarDatos()">Guardar Pago</button>
      </form>
    </section>

    <section class="card">
     <form id="searchForm" class="toolbar">
          <input type="search" id="search" placeholder="Buscar pago a proveedor...">
          <button class="btn1" type="submit" id="btnBuscar">Buscar</button>
     </form>
      <h2>Lista de Pagos a Proveedores</h2>

      <div id="resultado">
      </div>

</section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="pago.js"></script>
        <script
        type="module"
        src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script
        nomodule
        src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
    </html>