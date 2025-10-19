<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proveedores</title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include '../SideBar/sidebar.php'; ?>

  <div class="main">
    <header>
      <h1>Gestión de Proveedores</h1>
    </header>

    <section class="card">
      <h2>Agregar Proveedor</h2>
      <form id="supplierForm">
        <div class="form-group">
          <label for="name">Nombre:</label>
          <input type="text" id="name" required>
        </div>
        <div class="form-group">
          <label for="ruc">NIT:</label>
          <input type="text" id="ruc">
        </div>
        <div class="form-group">
          <label for="email">Correo:</label>
          <input type="email" id="email">
        </div>
        <div class="form-group">
          <label for="phone">Teléfono:</label>
          <input type="tel" id="phone">
        </div>
        <div class="form-group">
          <label for="address">Dirección:</label>
          <input type="text" id="address">
        </div>
        <button type="submit" class="btn">Guardar Proveedor</button>
      </form>
    </section>

    <section class="card">
       <div class="toolbar">
        <input type="search" id="search" placeholder="Buscar proveedor...">
        <button class="btn" id="btnExport">Buscar</button>
      </div>  
      <h2>Lista de Proveedores</h2>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>NIT</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Dirección</th>
          </tr>
        </thead>
        <tbody id="supplierTable">
          <tr>
            <td>Ejemplo S.A.</td>
            <td>1234567-8</td>
            <td>contacto@ejemplo.com</td>
            <td>5555-1234</td>
            <td>Ciudad, Guatemala</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>

    </div>
    </body>
    </html>