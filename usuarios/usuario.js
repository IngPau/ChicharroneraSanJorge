// Cuando se cargue la página
document.addEventListener("DOMContentLoaded", () => {
  const btnAgregar = document.getElementById("btnAgregar");
  if (btnAgregar) {
    btnAgregar.addEventListener("click", abrirFormularioUsuario);
  }
});

// 🧭 Función que abre el formulario modal
function abrirFormularioUsuario() {
  fetch("roles.php")
    .then(res => res.json())
    .then(roles => {
      if (!roles.length) {
        Swal.fire("Sin roles", "No hay roles registrados en la base de datos", "warning");
        return;
      }

      const opcionesRol = roles
        .map(rol => `<option value="${rol.id_rol}">${rol.nombre_rol}</option>`)
        .join("");

      Swal.fire({
        title: 'Agregar Usuario',
        html: `
          <form id="formUsuario" class="form-swal">
            <div class="campo">
              <label for="name">Nombre de usuario</label>
              <input type="text" id="name" class="swal2-input" placeholder="Ej: Sergio Izeppi">
            </div>
            <div class="campo">
              <label for="email">Correo electrónico</label>
              <input type="email" id="email" class="swal2-input" placeholder="Ej: sergio@example.com">
            </div>
            <div class="campo">
              <label for="password">Contraseña</label>
              <input type="password" id="password" class="swal2-input" placeholder="Contraseña">
            </div>
            <div class="campo">
              <label for="rol">Rol</label>
              <select id="rol" class="swal2-select">
                <option value="">Seleccione un rol</option>
                ${opcionesRol}
              </select>
            </div>
          </form>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
          const name = document.getElementById("name").value.trim();
          const email = document.getElementById("email").value.trim();
          const password = document.getElementById("password").value.trim();
          const rol = document.getElementById("rol").value.trim();

          if (!name || !email || !password || !rol) {
            Swal.showValidationMessage("Todos los campos son obligatorios");
            return false;
          }
          return { name, email, password, rol };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const { name, email, password, rol } = result.value;
          guardarUsuario(name, email, password, rol);
        }
      });
    })
    .catch(error => {
      console.error("Error cargando roles:", error);
      Swal.fire("Error", "No se pudieron cargar los roles", "error");
    });
}

// Función para guardar usuario
function guardarUsuario(name, email, password, rol) {
  Swal.fire({
    title: 'Guardando...',
    text: 'Por favor espere',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  fetch(`ingresousuarios.php?name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&rol=${encodeURIComponent(rol)}`)
    .then(response => response.json())
    .then(data => {
      Swal.close();
      if (data.estado === 2) {
        Swal.fire({
          title: "Usuario agregado",
          text: data.mensaje || "Se agregó correctamente",
          icon: "success"
        });
        // Aquí puedes actualizar la tabla si ya existe una función para eso
        if (typeof Traer_todos === "function") Traer_todos();
      } else {
        Swal.fire("Error", data.mensaje || "No se pudo agregar el usuario", "error");
      }
    })
    .catch(error => {
      Swal.close();
      Swal.fire("Error de conexión", "No se pudo conectar con el servidor: " + error.message, "error");
      console.error('Error:', error);
    });
}



function Traer_todos(searchTerm = '') {
    $("#resultado").html('<div>Cargando usuarios...</div>');

    $.get("mostrarusuarios.php", { search: searchTerm }, function(data) {
        if (data.tabla) {
            $("#resultado").html(data.tabla);
            // Asegurarnos de que registros se llene correctamente
            window.registros = data.datos || [];
            console.log("Usuarios cargados:", window.registros); // Para debug
        } else {
            $("#resultado").html('<div>Error en la estructura de datos recibida.</div>');
        }
    }, "json")
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al traer los usuarios:", textStatus, errorThrown, jqXHR.responseText);
        $("#resultado").html('<div>Error al comunicar con el servidor. Revisa la consola para detalles.</div>');
    });
}

$(document).ready(function() {
    Traer_todos(); 

    $("#searchForm").submit(function(e) {
        e.preventDefault(); 
        
        const searchTerm = $("#search").val(); 
        
        Traer_todos(searchTerm);
    });
});

function eliminarusuarios(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el usuario de forma permanente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("eliminarusuarios.php?id_usuario=" + encodeURIComponent(id))
                .then(response => response.json())
                .then(data => {
                    if (data.estado) {
                        Swal.fire({
                            title: "Usuario eliminado",
                            text: "El usuario se eliminó correctamente",
                            icon: "success"
                        });
                        Traer_todos(); // Recarga la tabla
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.mensaje || "No se pudo eliminar el usuario",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error de conexión",
                        text: "No se pudo conectar con el servidor: " + error.message,
                        icon: "error"
                    });
                    console.error("Error al eliminar:", error);
                });
        }
    });
}

function editarDatos(numeroFila) {
    let datosNuevos = {};

    const tabla = document.querySelector("#resultado table");
    const fila = tabla.rows[numeroFila + 1];
    
    const id = fila.cells[0].innerText.trim();
    const nombre = fila.cells[1].innerText.trim();
    const correo = fila.cells[2].innerText.trim();
    const contraseña = fila.cells[3].innerText.trim();
    const rol = fila.cells[4].innerText.trim();

    // Compara con los datos originales guardados en 'registros'
    if (nombre !== registros[numeroFila].nombre_usuario) {
        datosNuevos.name = nombre;
    }
    if (correo !== registros[numeroFila].correo_usuario) {
        datosNuevos.email = correo;
    }
    if (contraseña !== registros[numeroFila].contraseña_usuario) {
        datosNuevos.password = contraseña;
    }
    if (rol !== registros[numeroFila].nombre_rol) { 
        datosNuevos.rol = rol;
    }

    // Si hay cambios, los envía al servidor
    if (Object.keys(datosNuevos).length > 0) {
        datosNuevos.id = id;

        fetch("editarusuarios.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datosNuevos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.estado) {
                Swal.fire({
                    title: "Usuario actualizado",
                    text: "Los datos se actualizaron correctamente",
                    icon: "success"
                });
                Traer_todos();
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Los datos no se actualizaron",
                    icon: "error"
                });
            }
        })
        .catch(error => {
            console.error("Error al actualizar:", error);
            Swal.fire({
                title: "Error de conexión",
                text: "No se pudo conectar con el servidor: " + error.message,
                icon: "error"
            });
        });
    } else {
        Swal.fire({
            title: "Sin cambios",
            text: "No has modificado ningún dato para actualizar.",
            icon: "info"
        });
    }
}

// Función para abrir modal de edición
function abrirModalEdicion(id) {
    // Buscar el usuario en los registros
    const usuario = registros.find(user => user.id_usuario == id);
    
    if (!usuario) {
        Swal.fire("Error", "No se encontró el usuario", "error");
        return;
    }

    // Cargar roles disponibles
    fetch("roles.php")
        .then(res => res.json())
        .then(roles => {
            if (!roles.length) {
                Swal.fire("Sin roles", "No hay roles registrados", "warning");
                return;
            }

            const opcionesRol = roles
                .map(rol => `<option value="${rol.id_rol}" ${rol.id_rol == usuario.id_rol ? 'selected' : ''}>${rol.nombre_rol}</option>`)
                .join("");

            Swal.fire({
                title: 'Editar Usuario',
                html: `
                    <form id="formEditarUsuario" class="form-swal">
                        <div class="campo">
                            <label for="editName">Nombre de usuario</label>
                            <input type="text" id="editName" class="swal2-input" value="${usuario.nombre_usuario}" placeholder="Nombre de usuario">
                        </div>
                        <div class="campo">
                            <label for="editEmail">Correo electrónico</label>
                            <input type="email" id="editEmail" class="swal2-input" value="${usuario.correo_usuario}" placeholder="Correo electrónico">
                        </div>
                        <div class="campo">
                            <label for="editPassword">Contraseña</label>
                            <input type="password" id="editPassword" class="swal2-input" placeholder="Dejar vacío para no cambiar">
                            <small style="color: #666; font-size: 12px;">Dejar vacío para mantener la contraseña actual</small>
                        </div>
                        <div class="campo">
                            <label for="editRol">Rol</label>
                            <select id="editRol" class="swal2-select">
                                ${opcionesRol}
                            </select>
                        </div>
                    </form>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const name = document.getElementById("editName").value.trim();
                    const email = document.getElementById("editEmail").value.trim();
                    const password = document.getElementById("editPassword").value.trim();
                    const rolId = document.getElementById("editRol").value;

                    if (!name || !email || !rolId) {
                        Swal.showValidationMessage("Nombre, correo y rol son obligatorios");
                        return false;
                    }
                    return { name, email, password, rolId }; // Cambiado: enviar rolId
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { name, email, password, rolId } = result.value;
                    actualizarUsuario(id, name, email, password, rolId); // Cambiado: pasar rolId
                }
            });
        })
        .catch(error => {
            console.error("Error cargando roles:", error);
            Swal.fire("Error", "No se pudieron cargar los roles", "error");
        });
}

// Función para actualizar usuario - MODIFICADA
function actualizarUsuario(id, name, email, password, rolId) {  // Cambiado: recibir rolId en lugar de rol
    Swal.fire({
        title: 'Actualizando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    const datosActualizar = {
        id: id,
        name: name,
        email: email,
        rol: rolId  // Cambiado: enviar el ID del rol, no el nombre
    };

    // Solo incluir password si se cambió
    if (password && password !== "") {
        datosActualizar.password = password;
    }

    fetch("editarusuarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosActualizar)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.estado) {
            Swal.fire({
                title: "Usuario actualizado",
                text: "Los datos se actualizaron correctamente",
                icon: "success"
            });
            Traer_todos();
        } else {
            Swal.fire({
                title: "Error",
                text: data.mensaje || "Los datos no se actualizaron",
                icon: "error"
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor: " + error.message,
            icon: "error"
        });
        console.error("Error al actualizar:", error);
    });
}
