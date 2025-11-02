<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login/login.php");
  exit();
}
require_once 'conexion.php';
$db = conectar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú Principal</title>
  <link rel="stylesheet" href="menu.css">
  <link rel="stylesheet" href="SideBar/sidebar.css">
  <link rel="stylesheet" href="globales.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <?php include_once 'SideBar/sidebar.php'; ?>

    <main class="main">
      <header class="header">
        <h1>Chicharronera San Jorge</h1>
		<h3>Sistema dedicado a gestionar el control dentro del restaurante</h3>
      </header>

      <!-- ===================== INVENTARIO ===================== -->
      <section class="menu-section">
            <?php if (puedeVerModulo('Inventario')): ?>
        <h2>Inventario</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-warehouse icon"></i>
            <button class="menu-btn" onclick="location.href='/inventario/materiaPrima/inventarioMP.php'">Inventario Materia Prima</button>
            <p>Control de existencias de materia prima.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-chair icon"></i>
            <button class="menu-btn" onclick="location.href='/inventario/mobiliario/inventarioMobiliario.php'">Inventario Mobiliario</button>
            <p>Gestión del mobiliario y equipo disponible.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-triangle-exclamation icon"></i>
            <button class="menu-btn" onclick="location.href='/inventario/pérdidas/inventarioPerdidas.php'">Pérdidas</button>
            <p>Registro de pérdidas y deterioro de productos.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== RECURSOS HUMANOS ===================== -->
      <section class="menu-section">
            <?php if (puedeVerModulo('RRHH')): ?>
        <h2>Recursos Humanos</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-id-badge icon"></i>
            <button class="menu-btn" onclick="location.href='/puestos/puestos.php'">Puestos</button>
            <p>Gestión de cargos y salarios base.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-users icon"></i>
            <button class="menu-btn" onclick="location.href='/empleados/empleados.php'">Empleados</button>
            <p>Información del personal activo.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-money-check-dollar icon"></i>
            <button class="menu-btn" onclick="location.href='/planilla/planilla.php'">Planilla</button>
            <p>Control de nómina mensual.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== PROVEEDORES ===================== -->
      <section class="menu-section">
        <?php if (puedeVerModulo('Proveedores')): ?>
        <h2>Proveedores</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-truck-field icon"></i>
            <button class="menu-btn" onclick="location.href='/proveedores/proveedores.php'">Proveedores</button>
            <p>Gestión de proveedores registrados.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-hand-holding-dollar icon"></i>
            <button class="menu-btn" onclick="location.href='/proveedores/pagosproveedores.php'">Pago a Proveedor</button>
            <p>Registro de pagos y cuentas por pagar.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== OPERACIONES ===================== -->
      <section class="menu-section">
        <?php if (puedeVerModulo('Operaciones')): ?>
        <h2>Operaciones</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-cart-shopping icon"></i>
            <button class="menu-btn" onclick="location.href='/compras/compras.php'">Compras</button>
            <p>Gestión de órdenes de compra.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-store icon"></i>
            <button class="menu-btn" onclick="location.href='/ventas/ventas.php'">Ventas</button>
            <p>Control de ventas realizadas.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-users-line icon"></i>
            <button class="menu-btn" onclick="location.href='/clientes/clientes.php'">Clientes</button>
            <p>Administración de clientes frecuentes.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-utensils icon"></i>
            <button class="menu-btn" onclick="location.href='/Platos/platos.php'">Platos</button>
            <p>Gestión del menú principal del restaurante.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-bowl-food icon"></i>
            <button class="menu-btn" onclick="location.href='#'">Recetas</button>
            <p>Control de ingredientes y preparación de platillos.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== GESTIÓN COMERCIAL ===================== -->
      <section class="menu-section">
        <?php if (puedeVerModulo('Gestión Comercial')): ?>
        <h2>Gestión Comercial</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-shop icon"></i>
            <button class="menu-btn" onclick="location.href='/sucursales/sucursales.php'">Sucursales</button>
            <p>Administración de las diferentes sedes.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-chair icon"></i>
            <button class="menu-btn" onclick="location.href='/mesas/mesas.php'">Mesas</button>
            <p>Gestión de mesas en el área de atención al cliente.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-truck icon"></i>
            <button class="menu-btn" onclick="location.href='/vehiculos/vehiculos.php'">Vehículos</button>
            <p>Control de transporte y distribución.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== REPORTES Y ANÁLISIS ===================== -->
      <section class="menu-section">
        <?php if (puedeVerModulo('Reportes y Análisis')): ?>
        <h2>Reportes y Análisis</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-file-lines icon"></i>
            <button class="menu-btn" onclick="location.href='/ventas/reporte_ventas.php'">Reporte de Ventas</button>
            <p>Consulta detallada de ventas por periodo.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-brain icon"></i>
            <button class="menu-btn" onclick="location.href='/bi/businessIntelligence.php'">Business Intelligence</button>
            <p>Visualización avanzada de indicadores.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-chart-pie icon"></i>
            <button class="menu-btn" onclick="location.href='/dashboard/dashboard.php'">Dashboard</button>
            <p>Panel de métricas generales del negocio.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- ===================== ADMINISTRACIÓN DEL SISTEMA ===================== -->
      <section class="menu-section">
        <?php if (puedeVerModulo('Administración del Sistema')): ?>
        <h2>Administración del Sistema</h2>
        <div class="menu-container">
          <div class="menu-card">
            <i class="fa-solid fa-user-gear icon"></i>
            <button class="menu-btn" onclick="location.href='/usuarios/usuarios.php'">Usuarios</button>
            <p>Gestión de cuentas del sistema.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-user-shield icon"></i>
            <button class="menu-btn" onclick="location.href='/roles/roles.php'">Roles</button>
            <p>Configuración de permisos y jerarquías.</p>
          </div>

          <div class="menu-card">
            <i class="fa-solid fa-lock icon"></i>
            <button class="menu-btn" onclick="location.href='/permisos/permisos.php'">Permisos</button>
            <p>Control de acceso a los diferentes módulos.</p>
          </div>
        </div>
        <?php endif; ?>
      </section>

    </main>
  </div>
</body>
</html>
