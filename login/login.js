const inputPassword = document.querySelector('input[name="password"]');
const iconoPassword = document.querySelector('.icono-contraseña');

function cambiocontraseña() {
  if (inputPassword.type === 'password') {
    inputPassword.type = 'text';
    iconoPassword.src = '../imagenes/contra2.png'; // Cambia el icono
  } else {
    inputPassword.type = 'password';
    iconoPassword.src = '../imagenes/contra.png'; // Cambia el icono
  }
}

iconoPassword.addEventListener('click', cambiocontraseña);