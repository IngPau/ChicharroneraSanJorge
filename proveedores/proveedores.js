function guardarDatos(){
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const address = document.getElementById("address").value;

    // Validación básica
    if (!name.trim()) {
        Swal.fire({
            title: "Error",
            text: "El nombre del proveedor es obligatorio",
            icon: "error"
        });
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("ingresoproveedores.php?name=" + encodeURIComponent(name) + "&email=" + encodeURIComponent(email) + "&phone=" + encodeURIComponent(phone) + "&address=" + encodeURIComponent(address))
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        
        if (data.estado === 2) {
            Swal.fire({
                title: "Registro exitoso",
                text: data.mensaje || "Los datos se guardaron correctamente",
                icon: "success"
            });
            // Limpiar formulario
            document.getElementById("name").value = "";
            document.getElementById("email").value = "";
            document.getElementById("phone").value = "";
            document.getElementById("address").value = "";
        } else {
            Swal.fire({
                title: "Error",
                text: data.mensaje || "Los datos no se guardaron",
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
        console.error('Error:', error);
    });
}


function Traer_todos(searchTerm = '') {
    $("#resultado").html('<div>Cargando datos...</div>');

    $.get("mostrarproveedores.php", { search: searchTerm }, function(data) {
        
        if (data.tabla) {
            $("#resultado").html(data.tabla);
            registros = data.datos;
        } else {
             $("#resultado").html('<div>Error en la estructura de datos recibida.</div>');
        }

    }, "json")
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al traer los datos:", textStatus, errorThrown, jqXHR.responseText);
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


function editarDatos(numeroFila) {
    let datosNuevos = {};

    var tabla = document.querySelector("#resultado table");
    var fila = tabla.rows[numeroFila + 1];
    
    var id = fila.cells[0].innerText;
    var name = fila.cells[1].innerText;
    var email = fila.cells[2].innerText;
    var phone = fila.cells[3].innerText;
    var address = fila.cells[4].innerText;

    if(name != registros[numeroFila].name){
        datosNuevos.name = name;
    }
    if(email != registros[numeroFila].email){
        datosNuevos.email = email;
    }
    if(phone != registros[numeroFila].phone){
        datosNuevos.phone = phone;
    }
    if(address != registros[numeroFila].address){
        datosNuevos.address = address;
    }

    if(Object.keys(datosNuevos).length > 0){
        datosNuevos.id = id;
        fetch("editarproveedores.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datosNuevos)
        })
        .then(Response => Response.json())
        .then(data => {
            if (data.estado) {
                Swal.fire({
                    title: "Registro actualizado",
                    text: "Los datos se actualizaron correctamente",
                    icon: "success"
                });
                Traer_todos();
            }
            else {
                Swal.fire({
                    title: "Error",
                    text: "Los datos no se actualizaron",
                    icon: "error"
                });
            }
        });
    }
    else {
        Swal.fire({
            title: "Datos sin cambios",
            text: "Los datos no han sido modificados para actualizar.",
            icon: "question"
        });
    }
}

function eliminarDatos(id) {
    fetch("eliminarproveedores.php?id_proveedor=" + encodeURIComponent(id))
    .then(Response => Response.json())
        .then(data => {
            if (data.estado) {
                Swal.fire({
                    title: "Registro eliminado",
                    text: "Los datos se eliminaron correctamente",
                    icon: "success"
                });
                Traer_todos();
            }
            else {
                Swal.fire({
                    title: "Error",
                    text: "Los datos no se eliminaron",
                    icon: "error"
                });
            }
        });
}