	<?php

	?>
	<!DOCTYPE html>
	<html lang="es">
	<head>
	  <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <title>Dashboard Restaurante</title>
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
			  <li><a href="clientes.php">Clientes</a></li>
			  <li><a href="proveedores.php">Proveedores</a></li>
			  <li><a href="inventario.php">Inventario</a></li>
			  <li><a href="planilla.php">Planilla</a></li>
			  <li><a href="compras/compras.php">Compras</a></li>
			  <li><a href="servicio_domicilio.php">Servicio a Domicilio</a></li>
			  <li><a href="sucursales.php">Sucursales</a></li>
			  <li><a href="vehiculos.php">Vehículos</a></li>
			  <li><a href="bi.php">Business Intelligence</a></li>
			  <li><a href="reportes.php">Reportes</a></li>
			</ul>
		  </nav>
		</aside>

		<!-- Main Dashboard -->
		<main class="main">
		  <header class="header">
			<button class="menu-toggle" onclick="toggleMenu()">☰</button>
			<h1>Dashboard Principal</h1>
		  </header>

		  <section class="cards">
			<div class="card">
			  <h3>Ventas del Día</h3>
			  <p class="number">Q2,450.00</p>
			  <span class="success">+15.3% vs ayer</span>
			</div>
			<div class="card">
			  <h3>Órdenes Activas</h3>
			  <p class="number">23</p>
			  <span>5 pendientes</span>
			</div>
			<div class="card">
			  <h3>Clientes Registrados</h3>
			  <p class="number">1,247</p>
			  <span class="success">+8 nuevos hoy</span>
			</div>
			<div class="card">
			  <h3>Productos en Stock</h3>
			  <p class="number">156</p>
			  <span class="danger">3 ítems bajos</span>
			</div>
		  </section>

		  <section class="content">
			<div class="chart">
			  <h3>Ventas Semanales</h3>
			  <canvas id="salesChart"></canvas>
			  <div class="chart-footer">
				<p>Total Semanal: <strong>Q38,900</strong></p>
				<p>Promedio Diario: <strong>Q5,557</strong></p>
				<p>Mejor Día: <strong>Sábado</strong></p>
			  </div>
			</div>

			<div class="activity">
			  <h3>Actividad Reciente</h3>
			  <ul>
				<li>Nueva venta completada - Mesa 5 <span class="success">Q45.50</span></li>
				<li>Nuevo cliente registrado: María González</li>
				<li>Stock bajo: Tomates (4 Cajas) </li>
				<li>Pedido a domicilio entregado <span class="success">Q32.75</span></li>
			  </ul>
			</div>
		  </section>
		</main>
	  </div>

	  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	  <script src="script.js"></script>
	</body>
	</html>
