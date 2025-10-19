<?php
// Aquí podrías cargar datos dinámicos de sucursales desde la BD si lo deseas
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Sucursales</title>
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include '../SideBar/sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main">
      <h1>Gestión de Sucursales</h1>
  

      <div class="map-container">
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

  <!-- Ubicación 1 -->
  <div>
    <h3>Chicharronera San Jorge - Zona 8</h3>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d32614.57263975926!2d-90.56511933128692!3d14.618173800000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8589a10a97a3f98b%3A0x8a3e5368c85f8a9f!2sChicharronera%20San%20Jorge!5e1!3m2!1ses!2sgt!4v1758813953598!5m2!1ses!2sgt" 
      width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>

  <!-- Ubicación 2 -->
  <div>
    <h3>Chicharronera San Jorge - Zona 5</h3>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d32614.57263975926!2d-90.56511933128692!3d14.618173800000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8589a244de2c281b%3A0x960901947767df68!2sChicharronera%20San%20Jorge!5e1!3m2!1ses!2sgt!4v1758813990644!5m2!1ses!2sgt" 
      width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>

  <!-- Ubicación 3 -->
  <div>
    <h3>Chicharronera San Jorge - PeriSur</h3>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d32617.84638804202!2d-90.58490862089845!3d14.596107099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8589a1eec1101e99%3A0xc8dced6baeb3c109!2sChicharronera%20San%20Jorge%20PeriSur2!5e1!3m2!1ses!2sgt!4v1758814023670!5m2!1ses!2sgt" 
      width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>

  <!-- Ubicación 4 -->
  <div>
    <h3>Chicharronera San Jorge - Zona 7</h3>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28908.00215550886!2d-90.59507560393371!3d14.628451440216415!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8589a030eaa7319f%3A0x18e3fb8946e278e8!2sSan%20Jorge%20Chicharronera!5e1!3m2!1ses!2sgt!4v1758813491138!5m2!1ses!2sgt" 
      width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
    </iframe>
  </div>

</div>




    </main>
  </div>
   <footer>
        <p>© 2025 Chicharonera San Jorge</p>
      </footer>
</body>
</html>
