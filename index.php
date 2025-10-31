<?php
session_start(); // Iniciar la sesión
//Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
  header("Location: ../login/login.php");
  exit();
}
?>

<?php
require_once 'conexion.php';
$db = conectar();
date_default_timezone_set('America/Guatemala'); // Establece la zona horaria correcta

$hoy = date('Y-m-d');

$res = $db->query("SELECT SUM(total_venta) AS total FROM ventas WHERE fecha_venta = '$hoy'");
$totalDia = $res->fetch_assoc()['total'] ?? 0;
?>


	<!DOCTYPE html>
	<html lang="es">
	<head>
	  <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <title>Menu Principal</title>
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
			<h1>Menu Principal</h1>
		  </header>
