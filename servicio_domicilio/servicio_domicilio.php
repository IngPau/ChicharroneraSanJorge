<?php
// Aquí puedes enlazar con la BD para guardar pedidos
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Servicio a Domicilio</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"> <!-- CSS principal -->
<link rel="stylesheet" href="servicio.css"> <!-- CSS del módulo de servicio -->
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
        <li><a class="active" href="servicio_domicilio.php">Servicio a Domicilio</a></li>
        <li><a href="sucursales.php">Sucursales</a></li>
        <li><a href="vehiculos.php">Vehículos</a></li>
        <li><a href="bi.php">Business Intelligence</a></li>
        <li><a href="reportes.php">Reportes</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main">
    <header class="header">
      <h1>Servicio a Domicilio</h1>
    </header>

    <!-- Servicio a domicilio -->
    <div class="servicio-domicilio">
      <div class="servicio-container">
        <!-- Menú -->
        <section class="menu">
          <h2>Menú</h2>
          <div class="menu-list">
            <div class="menu-item">
              <h3>Chicharrón</h3>
              <span>Q15.00</span>
              <button>Agregar</button>
            </div>
            <div class="menu-item">
              <h3>Tacos</h3>
              <span>Q10.00</span>
              <button>Agregar</button>
            </div>
            <div class="menu-item">
              <h3>Refresco</h3>
              <span>Q5.00</span>
              <button>Agregar</button>
            </div>
            <!-- Agrega más productos aquí -->
          </div>
        </section>

        <!-- Carrito -->
        <aside class="cart">
          <h2>Pedido</h2>
          <div class="cart-items">
            <div class="cart-item">
              <span>Producto</span>
              <span>Q0.00</span>
            </div>
            <!-- Productos agregados se mostrarán aquí -->
          </div>
          <div class="cart-summary">
            <div>Total: <span>Q0.00</span></div>
            <button class="checkout-btn" disabled>Realizar pedido</button>
          </div>
        </aside>
      </div>

      <!-- Modal Checkout (solo visual, no funcional sin JS) -->
      <section class="checkout-modal">
        <div class="checkout-card">
          <h3>Información de entrega</h3>
          <form method="post">
            <label>Nombre
              <input required placeholder="Nombre completo">
            </label>
            <label>Teléfono
              <input required placeholder="+502 0000 0000">
            </label>
            <label>Dirección
              <textarea required placeholder="Dirección completa"></textarea>
            </label>
            <div class="form-actions">
              <button type="button" class="btn">Cancelar</button>
              <button type="submit" class="btn primary">Confirmar pedido</button>
            </div>
          </form>
        </div>
      </section>

    </div>
  </main>
</div>
</body>
</html>

