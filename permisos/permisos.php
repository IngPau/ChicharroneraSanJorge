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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Permisos</title>
  <link rel="stylesheet" href="permisos.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>
  <div class="container">
    <?php include_once '../SideBar/sidebar.php'; ?>

    <div class="contenedor">
      <h1>Gestión de Permisos</h1>
      <button class="boton agregar" id="btnAgregar">+ Agregar Permiso</button>
      <div id="resultado_permisos"> <!-- CAMBIÉ ESTO: resultado → resultado_permisos -->
        <!-- Aquí se cargarán los permisos -->
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="permisos.js"></script>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>