	<?php

	?>
	<!DOCTYPE html>
	<html lang="es">
	<head>
	  <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <title>Dashboard Restaurante</title>
	  <link rel="stylesheet" href="style.css">
	  <link rel="stylesheet" href="SideBar/sidebar.css">
	    <link rel="stylesheet" href="globales.css">
	</head>
	<body>
	<div class="container">
		<?php include 'SideBar/sidebar.php'; ?>
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
			  <h3>Acerca del proyecto</h3>
			  <p></p>
			</div>
		  </section>
		</main>
	</div>

	  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	  <script src="script.js"></script>
	</body>
	</html>
