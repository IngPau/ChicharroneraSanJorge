const seleccionSucursal = document.getElementById('sucursal');
const contenedorDetalles = document.querySelector(".contenedorDetalles");
const contenedorFormulario = document.querySelector(".formularioIngreso");
const contenedorActualizar = document.querySelector(".actualizarInventario.perdida")
const btnAgregar = document.getElementById('agregarMobiliario');
const iconoCerrar = document.getElementById("iconoCerrarLista");
const iconoCierre = document.getElementById("iconoCerrar");
const btnActualizar = document.getElementById("btnActualizar");

seleccionSucursal.addEventListener('change', function() {
    const tituloSucursal = document.getElementById('nombreSucursal');
    const sucursalSeleccionada = this.value;
    tituloSucursal.textContent = sucursalSeleccionada.charAt(0).toUpperCase() + sucursalSeleccionada.slice(1).replace(/([A-Z])/g, ' $1').trim();
    cargarInventario(sucursalSeleccionada);
});

btnAgregar.addEventListener('click', function() {
    const inputSucursal = document.getElementById("sucursalSeleccionada");
    const sucursalSeleccionada = seleccionSucursal ? seleccionSucursal.value : (document.getElementById('sucursal')?.value || '');
    contenedorDetalles.style.display = "block";
    contenedorFormulario.style.display = "block";
    inputSucursal.value = sucursalSeleccionada;
    inputSucursal.readOnly = true;
    agregarPérdida(sucursalSeleccionada);
});

iconoCerrar.addEventListener('click', function() {
    contenedorDetalles.style.display = "none";
    contenedorFormulario.style.display = "none";
});

/* ------------------------------------------ Cargar Datos de Inventario -------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    const sucursalInicial = "Perisur";
    cargarInventario(sucursalInicial);
});

function cargarInventario(sucursal) {
    const ENDPOINT = 'datosPerdidas.php'; 
    let paginaActual = 1;
    const tabla = document.getElementById('datosPerdida'); 

    const contenedorFiltros = document.querySelector('.filtrosInventarios');
    const inputDescripcion = contenedorFiltros ? contenedorFiltros.querySelector('#descripcion') : null;
    const btnBuscar = document.getElementById('buscar');

    const contadorPaginasEl = document.getElementById('contadorPaginas');
    const pagPrev = document.getElementById('paginacionAnterior');
    const pagNext = document.getElementById('paginacionSiguiente');

    if (!tabla) return; 

    let filtroDescripcion = '';

    function cargarDatos(pagina) {
        const params = new URLSearchParams({
            p: pagina,
            descripcion: filtroDescripcion,
            sucursal: sucursal
        });

        fetch(`${ENDPOINT}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                const { datos = [], totalPaginas = 1, paginaActual: paginaResp = 1 } = data;

                if(datos.length === 0){
                    tabla.innerHTML = '<tr><td colspan="5" style="text-align: center;">No se encontraron datos.</td></tr>';
                    actualizarPaginacion(1, 1);
                    return;
                }
                tabla.innerHTML = '';
                datos.forEach(fila => {
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-idPerdida', fila.idPerdida);
                    tr.innerHTML = `
                        <td>${fila.materiaPrima || ''}</td>
                        <td>${fila.motivo || ''}</td>
                        <td>${fila.cantidad || ''}</td>
                        <td>${fila.fecha || ''}</td>
                        <td>
                            <img class="iconoEditar" src="Editar.svg" alt="" style="cursor:pointer" />
                            <ion-icon class="iconoEliminar" name="trash-outline" style="font-size: 20px; color: #dc2626; cursor: pointer" title="Eliminar"></ion-icon>
                        </td>
                    `;
                    tabla.appendChild(tr);
                });
                actualizarPaginacion(totalPaginas, paginaResp);
                paginaActual = paginaResp;
            })
            .catch(err => {
                console.error('Error al cargar datos de pérdidas:', err);
            });
    }

    function actualizarPaginacion(totalPaginas, paginaActualLocal) {
        if (pagPrev) pagPrev.disabled = (paginaActualLocal <= 1);
        if (pagNext) pagNext.disabled = (paginaActualLocal >= totalPaginas);
        if (contadorPaginasEl) contadorPaginasEl.textContent = `Página ${paginaActualLocal} de ${totalPaginas}`;
    }

    // cargar inicial
    cargarDatos(paginaActual);

    if (pagPrev) pagPrev.addEventListener('click', () => {
        if (paginaActual > 1) cargarDatos(--paginaActual);
    });
    if (pagNext) pagNext.addEventListener('click', () => {
        cargarDatos(++paginaActual);
    });

    if (btnBuscar && inputDescripcion) {
        btnBuscar.addEventListener('click', (e) => {
            e.preventDefault();
            filtroDescripcion = inputDescripcion.value.trim();
            paginaActual = 1;
            cargarDatos(paginaActual);
        });

        inputDescripcion.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnBuscar.click();
            }
        });
    }
}

/* ------------------------------------------ Actualizar Datos de Pérdidas -------------------------- */
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("iconoEditar")) {
        contenedorDetalles.style.display = "block";
        contenedorActualizar.style.display = "block";
        actualizarInventario(e.target.closest('tr'));
    }
});

iconoCierre.addEventListener('click', function() {
    contenedorDetalles.style.display = "none";
    contenedorActualizar.style.display = "none";
    document.getElementById("textoResultado").style.display = "none";
});

function actualizarInventario(fila){
    let datosNuevos = {};
    const idPerdida = fila.getAttribute('data-idPerdida');
    const MateriaPrima = fila.children[0].textContent;
    const Motivo = fila.children[1].textContent;
    const Fecha = fila.children[3].textContent;
    const cantidad = fila.children[2].textContent;

    const tituloPerdida = document.getElementById("tituloPerdida");
    const inputMotivo = document.getElementById("motivoPerdida");
    const inputFecha = document.getElementById("fechaPerdida");
    const inputCantidad = document.getElementById("cantidadMP");

    tituloPerdida.textContent = "Actualizar Pérdida de " + MateriaPrima;
    inputMotivo.value = Motivo;
    inputFecha.value = Fecha;
    inputCantidad.value = cantidad;

    btnActualizar.onclick = function() {
        document.getElementById("textoResultado").style.display = "none";
        const nuevoMotivo = inputMotivo.value.trim();
        const nuevaFecha = inputFecha.value.trim();
        const nuevaCantidad = inputCantidad.value.trim();
        if(nuevoMotivo === Motivo && 
            nuevaFecha === Fecha && nuevaCantidad === cantidad){
            document.getElementById("textoResultado").style.display = "block";
            return;
        }
        if(nuevaCantidad < 1) {
            Swal.fire({
                title: "Error",
                text: "La cantidad debe ser mayor a 0.",
                icon: "error",
            });
            return;
        }
        if(nuevoMotivo!== "" && nuevaCantidad !== "" && nuevaFecha !== ""){
            if(nuevaCantidad !== cantidad){
                datosNuevos.cantidadAnterior = cantidad;
                datosNuevos.cantidad = nuevaCantidad;
            }
            if(nuevoMotivo !== Motivo){
                datosNuevos.motivo = nuevoMotivo;
            }
            if(nuevaFecha !== Fecha){
                datosNuevos.Fecha = nuevaFecha;
            }
        }
        if(Object.keys(datosNuevos).length > 0){
            fetch('actualizarPerdida.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codigo: idPerdida,
                    ...datosNuevos
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.estado == 1) {
                    contenedorDetalles.style.display = "none";
                    contenedorActualizar.style.display = "none";
                    document.getElementById("textoResultado").style.display = "none";
                    Swal.fire({
                    title: "Actualización exitosa",
                    text: "El registro se ha actualizado correctamente.",
                    icon: "success",
                    });
                    const sucursalActual = seleccionSucursal.value;
                    cargarInventario(sucursalActual);
                }
                else if (data.estado == 2) {
                    Swal.fire({
                        title: "Error",
                        text: "No hay suficiente inventario para aumentar la pérdida.",
                        icon: "error",
                    });
                }
                else {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo actualizar el registro.",
                        icon: "error",
                    });
                }  
            });            
        }
    }
}

/* ---------------------------- Función para eliminar ------------------*/
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("iconoEliminar")) {
        eliminarInventario(e.target.closest('tr'));
    }
});

function eliminarInventario(fila){
    const idInventario = fila.getAttribute('data-idPerdida');
    const mpSeleccionada = fila.children[0].textContent;
    Swal.fire({
        title: "Eliminación",
        text: "Se eliminará el registro de " + mpSeleccionada + " del las pérdidas de inventario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirmar eliminación",
        cancelButtonText: "Cancelar"
    }).then((result) => {
    if (result.isConfirmed) {
        fetch('eliminarPerdida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            codigo: idInventario
        })
        })
        .then(response => response.json())  
        .then(data => {
            if (data.estado == 1) {
                Swal.fire({
                    title: "Eliminado",
                    text: "El registro se ha eliminado correctamente.",
                    icon: "success"
                });
                const sucursalActual = seleccionSucursal.value;
                cargarInventario(sucursalActual);
            }
            else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo eliminar el registro",
                    icon: "error",
                });
            }
        });
    }
    });
}

/* ----------------------------------------------------------- Agregar Registro de Pérdida ---------------------------------------*/
function agregarPérdida(sucursal) {
    fetch('materiasPrimas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ sucursal: sucursal })
    })
    .then(response => response.text())
    .then(data => {
        const selectMateriaPrima = document.getElementById("materiaPrima");
        selectMateriaPrima.innerHTML = data; 
    })
    .catch(error => console.error('Error:', error));
}

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        const reason = urlParams.get('reason'); 
        if (status === 'success') {
            Swal.fire({
                title: "Registro Agregado",
                text: "El registro de pérdida se ha ingresado correctamente.",
                icon: "success"
            }).then(() => {
                const sucursalActual = seleccionSucursal.value;
                if (sucursalActual) {
                    cargarInventario(sucursalActual);
                }
            });
        } 
        else if (status === 'error') {
            if (reason === 'stock') {
                Swal.fire({
                    title: "Stock insuficiente",
                    text: "No hay suficiente inventario para registrar esta pérdida.",
                    icon: "warning",
                });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo agregar el registro.",
                    icon: "error",
                });
            }
        }
    }
    const url = new URL(window.location);
    url.searchParams.delete('status');
    url.searchParams.delete('reason'); 
    window.history.replaceState({}, document.title, url);
};


