let registros_permisos = [];

$(document).ready(function() {
    Traer_todos_Permisos(); // Cargar permisos al iniciar
    
    // Evento para el botón agregar
    $('#btnAgregar').on('click', function() {
        abrirFormularioPermiso();
    });
});

function Traer_todos_Permisos(searchTerm = '') {
    $("#resultado_permisos").html('<p>Cargando permisos...</p>');

    $.get("mostrarpermisos.php", { search: searchTerm }, function(data) {
        if (data.tabla) {
            $("#resultado_permisos").html(data.tabla);
            registros_permisos = data.datos; // Guardar datos para edición
        } else {
            $("#resultado_permisos").html('<p class="error">No se encontraron permisos.</p>');
        }
    }, "json")
    .fail(function(jqXHR) {
        $("#resultado_permisos").html('<p class="error">Error al comunicar con el servidor.</p>');
        console.error("Error:", jqXHR.responseText);
    });
}

function abrirFormularioPermiso() {
    Swal.fire({
        title: 'Agregar Nuevo Permiso',
        html: `
            <input type="text" id="nombre_permiso" class="swal2-input" placeholder="Ej: usuarios_crear" required>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = document.getElementById("nombre_permiso").value.trim();
            
            if (!nombre) {
                Swal.showValidationMessage("El nombre del permiso es obligatorio");
                return false;
            }
            return { nombre };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { nombre } = result.value;
            guardarPermiso(nombre);
        }
    });
}

function guardarPermiso(nombre) {
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // CORRECCIÓN: El archivo se llama ingresarpermisos.php (con S)
    $.get("ingresarpermisos.php", { nombre: nombre }, function(data) {
        Swal.close();
        if (data.estado == 2) {
            Swal.fire("¡Éxito!", data.mensaje, "success");
            Traer_todos_Permisos(); 
        } else {
            Swal.fire("Error", data.mensaje, "error");
        }
    }, "json")
    .fail(function(jqXHR) {
        Swal.close();
        Swal.fire("Error de Conexión", "No se pudo conectar con el servidor.", "error");
        console.error("Error AJAX:", jqXHR);
    });
}

function eliminarPermiso(id_permiso) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esto eliminará el permiso permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // CORRECCIÓN: El archivo se llama eliminarpermisos.php (con S)
            fetch("eliminarpermisos.php?id_permiso=" + encodeURIComponent(id_permiso))
                .then(response => response.json())
                .then(data => {
                    if (data.estado) {
                        Swal.fire("Eliminado", "Permiso eliminado correctamente.", "success");
                        Traer_todos_Permisos();
                    } else {
                        Swal.fire("Error", data.mensaje || "No se pudo eliminar el permiso.", "error");
                    }
                })
                .catch(error => {
                    Swal.fire("Error de conexión", "No se pudo conectar con el servidor.", "error");
                    console.error("Error:", error);
                });
        }
    });
}

function abrirFormularioEdicionPermiso(id_permiso) {
    const permiso_actual = registros_permisos.find(p => p.id_permiso == id_permiso);
    if (!permiso_actual) return Swal.fire("Error", "Permiso no encontrado.", "error");

    Swal.fire({
        title: 'Editar Permiso',
        html: `
            <input type="text" id="nombre_permiso_edit" class="swal2-input" 
                   value="${permiso_actual.nombre_permiso}" placeholder="Nombre del permiso">
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar Cambios',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = $('#nombre_permiso_edit').val().trim();
            if (!nombre) {
                Swal.showValidationMessage("El nombre es obligatorio");
                return false;
            }
            return { id_permiso, nombre };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarCambiosPermiso(result.value);
        }
    });
}

function guardarCambiosPermiso(datos) {
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    console.log("Datos a enviar:", datos);

    fetch("editarpermisos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(response => {
        console.log("Status de respuesta:", response.status);
        console.log("URL de respuesta:", response.url);
        
        return response.text().then(text => {
            console.log("Respuesta COMPLETA del servidor:", text);
            
            // Si la respuesta contiene HTML, es un error de PHP
            if (text.includes('<br />') || text.includes('<b>') || text.startsWith('<!')) {
                throw new Error("El servidor devolvió un error HTML:\n" + text.substring(0, 500));
            }
            
            try {
                const jsonData = JSON.parse(text);
                return jsonData;
            } catch (e) {
                throw new Error("No se pudo parsear JSON. Respuesta: " + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        Swal.close();
        console.log("Datos parseados:", data);
        if (data.estado) {
            Swal.fire("Actualizado", data.mensaje, "success");
            Traer_todos_Permisos();
        } else {
            Swal.fire("Error", data.mensaje, "error");
        }
    })
    .catch(error => {
        Swal.close();
        console.error("Error completo:", error);
        Swal.fire({
            title: "Error del Servidor",
            html: `<pre style="text-align: left; font-size: 12px; overflow: auto;">${error.message}</pre>`,
            icon: "error",
            width: 600
        });
    });
}