<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Restaurante</title>
    <link rel="stylesheet" href="../inventarioMP.css">
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
      <?php include 'contenedorActualizar.php'; ?>
      <!-- Main Dashboard -->
      <main class="main">
        <section class="inventarioMateriaPrima">
          <h1>Inventario Materia Prima</h1>
          <div class="filtrosInventarios">
            <div class="filtro">
              <label for="sucursal">Seleccione la sucursal</label>
              <select id="sucursal" name="sucursal">
                <option value="Perisur">Perisur</option>
                <option value="Zona 8 Ciudad">Zona 8 Ciudad</option>
                <option value="San Cristobal">San Cristobal</option>
                <option value="Monserrat">Monserrat</option>
              </select>
            </div>
            <div class="filtro">
              <label for="Descripcion">Buscar por Materia Prima</label>
              <input type="text" id="descripcion" name="descripcion" />
            </div>
            <button id="buscar">Buscar</button>
          </div>
          <div class="inventarioTabla">
            <h2 id="nombreSucursal">Perisur</h2>
            <table>
              <thead>
                <tr>
                  <th style="width: 10%">Código</th>
                  <th style="width: 50%">Materia Prima</th>
                  <th>Cantidad</th>
                  <th style="width: 12%; font-size: 14px">Unidad de Medida</th>
                  <th style="width: 10%">Acciones</th>
                </tr>
              </thead>
              <tbody id="datosMateriaPrima">
              </tbody>
            </table>
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
    <script src="inventarioMP.js"></script>
    <script
      type="module"
      src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"
    ></script>
    <script
      nomodule
      src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </body>
</html>