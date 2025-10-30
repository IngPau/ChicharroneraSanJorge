<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chicharronera San Jorge</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="login-container">
    <div class="formulario">
      <div class="circulo">
        <div class="circulo--2">
          <img src="/imagenes/logo.png" alt="Logo" />
        </div>
      </div>
      <div class="border">
        <h1>Chicharronera San Jorge</h1>
        <form method="POST" action="ingreso.php">
          <div class="campo">
            <label>Usuario</label>
            <input type="text" name="usuario" required placeholder="" />
          </div>
          <div class="campo">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="" />
            <img src="/imagenes/contra.png" alt="Icono de contraseña" class="icono-contraseña" draggable="false" />
          </div>

          <div class="recordar">
            ¿Olvidó su contraseña? <a href="olvidecontraseña.php">Recupérala</a>
          </div>
          <div class="boton">
          <input type="submit" value="LOGIN" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="login.js"></script>
</body>
</html>