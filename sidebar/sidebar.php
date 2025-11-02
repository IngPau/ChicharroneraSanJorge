<?php
include_once "verificarpermisos.php";

// DEBUG TEMPORAL - Esto se verá en el código fuente de la página
echo "<!-- ===== DEBUG INICIO ===== -->";
echo "<!-- Usuario ID: " . ($_SESSION['usuario_id'] ?? 'NO SESIÓN') . " -->";
echo "<!-- Rol ID: " . ($_SESSION['id_rol'] ?? 'NO ROL') . " -->";
echo "<!-- ¿Sesión activa?: " . (isset($_SESSION['usuario_id']) ? 'SÍ' : 'NO') . " -->";

// Probar cada permiso individualmente
$permisos_a_probar = ['Inventario', 'RRHH', 'Proveedores', 'Operaciones', 'Gestión Comercial', 'Reportes y Análisis', 'Administración del Sistema'];

foreach ($permisos_a_probar as $permiso) {
    echo "<!-- Permiso '$permiso': " . (tienePermiso($permiso) ? '✅ SÍ' : '❌ NO') . " -->";
}

echo "<!-- ===== DEBUG FIN ===== -->";
?>

<!-- Sidebar -->
<aside class="sidebar">
  <h2>Chicharronera San Jorge</h2>
  <nav>
    <ul>
      <!-- DASHBOARD -->
      <li><a href="/index.php">MenuPrincipal</a></li>

      <!-- INVENTARIO -->
      <?php if (puedeVerModulo('Inventario')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Inventario ▾</a>
        <ul class="submenu-items">
          <li><a href="/inventario/materiaPrima/inventarioMP.php">Inventario Materia Prima</a></li>
          <li><a href="/inventario/mobiliario/inventarioMobiliario.php">Inventario Mobiliario</a></li>
          <li><a href="/inventario/pérdidas/inventarioPerdidas.php">Pérdidas</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- RECURSOS HUMANOS -->
      <?php if (puedeVerModulo('RRHH')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">RRHH ▾</a>
        <ul class="submenu-items">
          <li><a href="/puestos/puestos.php">Puestos</a></li>
          <li><a href="/empleados/empleados.php">Empleados</a></li>
          <li><a href="/planilla/planilla.php">Planilla</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- PROVEEDORES -->
      <?php if (puedeVerModulo('Proveedores')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Proveedores ▾</a>
        <ul class="submenu-items">
          <li><a href="/proveedores/proveedores.php">Proveedores</a></li>
          <li><a href="/proveedores/pagosproveedores.php">Pago a Proveedor</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- OPERACIONES -->
      <?php if (puedeVerModulo('Operaciones')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Operaciones ▾</a>
        <ul class="submenu-items">
          <li><a href="/compras/compras.php">Compras</a></li>
          <li><a href="/ventas/ventas.php">Ventas</a></li>
          <li><a href="/clientes/clientes.php">Clientes</a></li>
          <li><a href="/Platos/platos.php">Platos</a></li>
          <li><a href="#">Recetas</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- GESTIÓN COMERCIAL -->
      <?php if (puedeVerModulo('Gestión Comercial')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Gestión Comercial ▾</a>
        <ul class="submenu-items">
          <li><a href="/sucursales/sucursales.php">Sucursales</a></li>
          <li><a href="/mesas/mesas.php">Mesas</a></li>
          <li><a href="/vehiculos/vehiculos.php">Vehículos</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- REPORTES -->
      <?php if (puedeVerModulo('Reportes y Análisis')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Reportes y Análisis ▾</a>
        <ul class="submenu-items">
          <li><a href="/ventas/reporte_ventas.php">Reporte de Ventas</a></li>
          <li><a href="/inventario/reporte_inventario_materiaprima.php">Reporte de inventario Materia prima</a></li>
          <li><a href="/inventario/reporte_inventario_mobiliario.php">Reporte de inventario Mobiliario</a></li>
          <li><a href="/reportes/reporte_clientes.php">Reporte de Clientes</a></li>
          <li><a href="/reportes/reporte_proveedores.php">Reporte de Proveedores</a></li>
          <li><a href="../reportes/reportes_compras.php">Reporte de Compras</a></li>
          <li><a href="/bi/businessIntelligence.php">Business Intelligence</a></li>
          <li><a href="/dashboard/dashboard.php">Dashboard</a></li>
        </ul>
      </li>
      <?php endif; ?>

      <!-- ADMINISTRACIÓN DEL SISTEMA -->
      <?php if (puedeVerModulo('Administración del Sistema')): ?>
      <li class="submenu">
        <a href="#" class="submenu-toggle">Administración del Sistema ▾</a>
        <ul class="submenu-items">
          <li><a href="/usuarios/usuarios.php">Usuarios</a></li>
          <li><a href="/roles/roles.php">Roles</a></li>
          <li><a href="/permisos/permisos.php">Permisos</a></li>
        </ul>
      </li>
      <?php endif; ?>

    </ul>
    
<div class="sidebar-footer">
    <button onclick="window.location.href='/logout/logout.php'" class="boton-salir">
       <img src="/imagenes/salirLogo.png" alt="Salir" class="icono-boton3" />
    </button>
</div>
  </nav>
</aside>
<script src="/sidebar/sidebar.js"></script>
