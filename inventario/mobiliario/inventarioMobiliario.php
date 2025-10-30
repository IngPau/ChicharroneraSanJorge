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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Restaurante</title>
    <link rel="stylesheet" href="../inventarioMP.css" />
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../globales.css">    
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <div class="container">
      <!-- Sidebar -->
      <?php include '../../sidebar/sidebar.php'; ?> 
      <?php include 'contenedorMobiliario.php'; ?>
      <!-- Main Dashboard -->
      <main class="main">
        <section class="inventarioMobiliario">
          <h1>Inventario Mobiliario y Equipo</h1>
          <div class="filtrosInventarios">
            <div class="filtro mobiliario">
              <label for="sucursal">Seleccione la sucursal</label>
              <select id="sucursal" name="sucursal">
                <option value="Perisur">Perisur</option>
                <option value="Zona 8 Ciudad">Zona 8 Ciudad</option>
                <option value="San Cristobal">San Cristobal</option>
                <option value="Monserrat">Monserrat</option>
              </select>
            </div>
            <div class="filtro mobiliario">
              <label for="descripcion">Buscar por Mobiliario</label>
              <input type="text" id="descripcion" name="descripcion" />
            </div>
            <div class="filtro mobiliario">
              <label for="categoriaMobiliario">Buscar por Categoria</label>
              <input type="text" id="categoriaMobiliario" name="categoriaMobiliario" />
            </div>
            <button id="buscar">Buscar</button>
          </div>
          <div class="inventarioTabla">
            <div class="encabezadoTabla">
              <h2 id="nombreSucursal">Perisur</h2>
              <button id="agregarMobiliario">
                <ion-icon name="add-circle-outline"></ion-icon>
                Agregar mobiliario
              </button>
            </div>
            <div class="tabla">
              <table>
                <thead>
                  <tr>
                    <th style="width: 5%">Código</th>
                    <th style="width: 25%">Mobiliario</th>
                    <th style="width: 45%">Descripción</th>
                    <th style="width: 20%;">Categoria</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody id="datosMobiliario">
                </tbody>
              </table>
            </div>
          </div>
          <div class="paginacion Materiales">
            <button class="btnPaginacion" id="paginacionAnterior">
              <ion-icon name="arrow-back-outline"></ion-icon>
              Anterior
            </button>
            <span id="contadorPaginas">Página 1 de 1</span>
            <button class="btnPaginacion" id="paginacionSiguiente">
              Siguiente
              <ion-icon name="arrow-forward-outline"></ion-icon>
            </button>
          </div>
        </section>
      </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="inventarioMobiliario.js"></script>
    <script
    type="module"
    src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script
    nomodule
    src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </body>
</html>
