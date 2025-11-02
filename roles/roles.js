let registros_roles = []; // Almacena los datos de la tabla de roles

$(document).ready(function() {
    Traer_todos_Roles();

    $('#btnAgregarRol').on('click', function() {
        abrirFormularioRol();
    });
});

function Traer_todos_Roles(searchTerm = '') {
    $("#resultado_roles").html('<p>Cargando roles...</p>');

    $.get("mostrarroles.php", { search: searchTerm }, function(data) {
        if (data.tabla) {
            $("#resultado_roles").html(data.tabla);
            registros_roles = data.datos;
        } else {
            $("#resultado_roles").html('<p class="error">No se encontraron roles o hubo un error de estructura.</p>');
        }
    }, "json")
    .fail(function(jqXHR) {
        $("#resultado_roles").html('<p class="error">Error al comunicar con el servidor.</p>');
        console.error("Error al traer los roles:", jqXHR.responseText);
    });
}

function abrirFormularioRol() {
    // Primero cargar los permisos disponibles
    Swal.fire({
        title: 'Cargando permisos...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // Obtener todos los permisos disponibles
    fetch('permisosrol.php?id_rol=0') // ID 0 para obtener todos los permisos sin asignar
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (!data.permisos) {
                Swal.fire("Error", "No se pudieron cargar los permisos", "error");
                return;
            }

            let permisosHTML = '';
            data.permisos.forEach(p => {
                permisosHTML += `
                    <div class="checkbox-group">
                        <input type="checkbox" id="permiso_nuevo_${p.id_permiso}" name="permisos[]" value="${p.id_permiso}">
                        <label for="permiso_nuevo_${p.id_permiso}">${p.nombre_permiso}</label>
                    </div>
                `;
            });

            Swal.fire({
                title: 'Agregar Nuevo Rol',
                width: '800px',
                html: `
                    <form id="formRol">
                        <div class="campo" style="text-align: left;">
                            <label for="nombre_rol">Nombre del Rol</label>
                            <input type="text" id="nombre_rol" class="swal2-input" placeholder="Ej: Administrador">
                        </div>
                        <div class="campo" style="text-align: left;">
                            <label for="descripcion_rol">Descripción</label>
                            <textarea id="descripcion_rol" class="swal2-textarea" placeholder="Breve descripción del rol"></textarea>
                        </div>
                        <h2 style="margin-top: 20px;">Asignación de Permisos</h2>
                        <div class="permisos-list" style="max-height: 300px; overflow-y: auto; text-align: left; padding: 10px; border: 1px solid #ddd;">
                            ${permisosHTML || '<p>No hay permisos disponibles</p>'}
                        </div>
                    </form>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById("nombre_rol").value.trim();
                    const descripcion = document.getElementById("descripcion_rol").value.trim();
                    const permisos_seleccionados = [];

                    // Recolectar permisos seleccionados
                    document.querySelectorAll('input[name="permisos[]"]:checked').forEach(checkbox => {
                        permisos_seleccionados.push(checkbox.value);
                    });

                    if (!nombre) {
                        Swal.showValidationMessage("El nombre del rol es obligatorio");
                        return false;
                    }
                    return { nombre, descripcion, permisos_seleccionados };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { nombre, descripcion, permisos_seleccionados } = result.value;
                    guardarRol(nombre, descripcion, permisos_seleccionados);
                }
            });
        })
        .catch(error => {
            Swal.close();
            Swal.fire("Error", "No se pudieron cargar los permisos", "error");
            console.error('Error:', error);
        });
}

function guardarRol(nombre, descripcion, permisos_seleccionados = []) {
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // Crear FormData para enviar los datos
    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    
    // Agregar cada permiso seleccionado
    permisos_seleccionados.forEach(permiso => {
        formData.append('permisos[]', permiso);
    });

    fetch('ingresoroles.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.estado === 2) {
            Swal.fire("Rol agregado", data.mensaje || "Rol y permisos guardados correctamente", "success");
            Traer_todos_Roles();
        } else {
            Swal.fire("Error", data.mensaje || "No se pudo agregar el rol", "error");
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire("Error de conexión", "No se pudo conectar con el servidor.", "error");
        console.error('Error:', error);
    });
}

function eliminarRol(id_rol) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el rol y sus permisos.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("eliminarroles.php?id_rol=" + encodeURIComponent(id_rol))
                .then(response => response.json())
                .then(data => {
                    if (data.estado) {
                        Swal.fire("Rol eliminado", "El rol se eliminó correctamente", "success");
                        Traer_todos_Roles();
                    } else {
                        Swal.fire("Error", data.mensaje || "No se pudo eliminar el rol", "error");
                    }
                })
                .catch(error => {
                    Swal.fire("Error de conexión", "No se pudo conectar con el servidor: " + error.message, "error");
                    console.error("Error al eliminar:", error);
                });
        }
    });
}

function editarRol(id_rol) {
    // La función find() ya solo accede a los campos que existen en la tabla (id_rol, nombre_rol, descripcion_rol)
    const rol_actual = registros_roles.find(r => r.id_rol == id_rol); 
    if (!rol_actual) return Swal.fire("Error", "Rol no encontrado.", "error");

    Swal.fire({
        title: 'Cargando permisos...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`permisosrol.php?id_rol=${id_rol}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            // Asumimos que data.rol solo contiene id_rol, nombre_rol, descripcion_rol
            if (data.permisos && data.rol) { 
                mostrarFormularioEdicionRol(data.rol, data.permisos);
            } else {
                Swal.fire("Error", "No se pudieron cargar los datos de edición.", "error");
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire("Error de conexión", "No se pudieron cargar los permisos.", "error");
            console.error('Error:', error);
        });
}

function mostrarFormularioEdicionRol(rol, permisos) {
    let permisosHTML = '';
    permisos.forEach(p => { 
        const checked = p.asignado == 1 ? 'checked' : '';
        permisosHTML += `
            <div class="checkbox-group">
                <input type="checkbox" id="permiso_${p.id_permiso}" name="permisos[]" value="${p.id_permiso}" ${checked}>
                <label for="permiso_${p.id_permiso}">${p.nombre_permiso}</label>
            </div>
        `;
    });

    Swal.fire({
        title: 'Editar Rol y Permisos',
        width: '800px',
        html: `
            <form id="formEdicionRol">
                <input type="hidden" id="id_rol" value="${rol.id_rol}">
                <div class="campo" style="text-align: left;">
                    <label for="nombre_rol_edit">Nombre del Rol</label>
                    <input type="text" id="nombre_rol_edit" class="swal2-input" value="${rol.nombre_rol}" placeholder="Nombre del Rol">
                </div>
                <div class="campo" style="text-align: left;">
                    <label for="descripcion_rol_edit">Descripción</label>
                    <textarea id="descripcion_rol_edit" class="swal2-textarea" placeholder="Descripción">${rol.descripcion_rol || ''}</textarea>
                </div>
                <h2 style="margin-top: 20px;">Asignación de Permisos</h2>
                <div class="permisos-list" style="max-height: 300px; overflow-y: auto; text-align: left; padding: 10px; border: 1px solid #ddd;">
                    ${permisosHTML}
                </div>
            </form>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar Cambios',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = $('#nombre_rol_edit').val().trim();
            const descripcion = $('#descripcion_rol_edit').val().trim();
            const permisos_seleccionados = [];
            
            // Recolectar IDs de permisos marcados
            $('#formEdicionRol input[name="permisos[]"]:checked').each(function() {
                permisos_seleccionados.push($(this).val());
            });

            if (!nombre) {
                Swal.showValidationMessage("El nombre del rol es obligatorio");
                return false;
            }
            return { id_rol: rol.id_rol, nombre, descripcion, permisos_seleccionados };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarCambiosRolYPermisos(result.value);
        }
    });
}

function guardarCambiosRolYPermisos(datos) {
    Swal.fire({
        title: 'Guardando...',
        text: 'Actualizando rol y permisos',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch("editarroles.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.estado) {
            Swal.fire("Actualizado", data.mensaje || "Rol y permisos guardados correctamente.", "success");
            Traer_todos_Roles();
        } else {
            Swal.fire("Error", data.mensaje || "No se pudo actualizar el rol.", "error");
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire("Error de conexión", "Error al enviar los datos al servidor.", "error");
        console.error("Error al guardar:", error);
    });
}