<?php
// Aquí en el futuro puedes conectar tu base de datos y cargar los productos
// require_once("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventario | Chicharronera San Jorge</title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
  <!-- Fuente Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include '../SideBar/sidebar.php'; ?>

    <!-- Main -->
    <main class="main">
      <header class="header">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <h1>Inventario</h1>
      </header>

      <section class="inventory">
        <button onclick="nuevoProducto()" class="btn-add">➕ Agregar Producto</button>
        <h2>Lista de Productos</h2>
        <table class="inventory-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Chicharrón</td>
              <td>Carnes</td>
              <td>50</td>
              <td>Q25.00</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Tortillas</td>
              <td>Acompañamientos</td>
              <td>200</td>
              <td>Q1.00</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>Refresco</td>
              <td>Bebidas</td>
              <td>100</td>
              <td>Q8.00</td>
              <td>
                <button class="btn-edit">Editar</button>
                <button class="btn-delete">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="script.js"></script>
</body>
</html>
