const seleccionSucursal = document.getElementById('sucursal');
const contenedorDetalles = document.querySelector(".contenedorDetalles");
const contenedorActualizar = document.querySelector(".actualizarInventario");
const iconoCerrarActualizar = document.getElementById("iconoCerrarLista");
const btnActualizar = document.getElementById("btnActualizar");
const contenedorFormulario = document.querySelector(".formularioIngreso");
const btnAgregar = document.getElementById('agregarMobiliario');
const iconoCerrar = document.getElementById("iconoCerrar");
let datosInventario = [];

seleccionSucursal.addEventListener('change', function() {
    const tituloSucursal = document.getElementById('nombreSucursal');
    const sucursalSeleccionada = this.value;
    tituloSucursal.textContent = sucursalSeleccionada.charAt(0).toUpperCase() + sucursalSeleccionada.slice(1).replace(/([A-Z])/g, ' $1').trim();
    cargarInventario(sucursalSeleccionada);
});

btnAgregar.addEventListener('click', function() {
    contenedorDetalles.style.display = "block";
    contenedorFormulario.style.display = "block";
});

iconoCerrar.addEventListener('click', function() {
    contenedorDetalles.style.display = "none";
    contenedorFormulario.style.display = "none";
});


document.addEventListener('DOMContentLoaded', function () {
    const sucursalInicial = seleccionSucursal.value;
    const tituloSucursal = document.getElementById('nombreSucursal');
    tituloSucursal.textContent = sucursalInicial.charAt(0).toUpperCase() + sucursalInicial.slice(1).replace(/([A-Z])/g, ' $1').trim();
    cargarInventario(sucursalInicial);
});

function cargarInventario(sucursal) {
    const ENDPOINT = 'datosInventarioMp.php'; 
    let paginaActual = 1;
    const tabla = document.getElementById('datosMateriaPrima'); 

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
                datosInventario = datos;
                tabla.innerHTML = '';
                datos.forEach(fila => {
                    const tr = document.createElement('tr');
                    const cantidad = parseFloat(fila.cantidad);
                    const stockMinimo = parseFloat(fila.stockMinimo);

                    if (!isNaN(cantidad) && !isNaN(stockMinimo) && cantidad <= stockMinimo) {
                        tr.classList.add('bajo-stock');
                    }
                    tr.setAttribute('data-idInventario', fila.idInventario);
                    tr.innerHTML = `
                        <td>${fila.codigo || ''}</td>
                        <td>${fila.materiaPrima || ''}</td>
                        <td>${fila.cantidad || ''}</td>
                        <td>${fila.unidadMedida || ''}</td>
                        <td>${fila.stockMinimo || ''}</td>
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
                console.error('Error al cargar datos materia prima:', err);
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

/* ------------------------------- Evento para mostrar el contenedor para actualizar ----------------------------------*/
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("iconoEditar")) {
        contenedorDetalles.style.display = "block";
        contenedorActualizar.style.display = "block";
        actualizarInventario(e.target.closest('tr'));
    }
});

iconoCerrarActualizar.addEventListener("click", function() {
    contenedorDetalles.style.display = "none";
    contenedorActualizar.style.display = "none";
    document.getElementById("textoResultado").style.display = "none";
});

function actualizarInventario(fila){
    const sucursalActual = seleccionSucursal.value;
    let datosNuevos = {};
    const idInventario = fila.getAttribute('data-idInventario');
    const materiaPrima = fila.children[1].textContent;
    const cantidad = fila.children[2].textContent;
    const unidadMedida = fila.children[3].textContent;
    const stockMinimo = fila.children[4].textContent;


    const tituloMateriaPrima = document.getElementById("materiaPrima");
    const inputCantidad = document.getElementById("cantidadMP");
    const inputUnidad = document.getElementById("unidadMedida");
    const inputCantidadMinima = document.getElementById("cantidadMinima");

    tituloMateriaPrima.textContent = "Actualizar Inventario de " + materiaPrima;
    inputCantidad.value = cantidad;
    inputUnidad.value = unidadMedida;
    inputCantidadMinima.value = stockMinimo;

    
    btnActualizar.onclick = function() {
        document.getElementById("textoResultado").style.display = "none";
        const nuevaCantidad = inputCantidad.value.trim();
        const nuevaUnidad = inputUnidad.value.trim();
        const nuevoStockMinimo = inputCantidadMinima.value.trim();

        if(nuevaCantidad === cantidad && nuevaUnidad === unidadMedida && nuevoStockMinimo === stockMinimo) {
            document.getElementById("textoResultado").style.display = "block";
            return;
        }
        if(nuevaCantidad < 0) {
            Swal.fire({
                title: "Error",
                text: "La cantidad debe ser mayor o igual a 0.",
                icon: "error",
            });
            return;
        }
        if(nuevoStockMinimo < 1) {
            Swal.fire({
                title: "Error",
                text: "La cantidad debe ser mayor a 0.",
                icon: "error",
            });
            return;
        }
        if(nuevaCantidad !== cantidad && nuevaCantidad >= 0){
            datosNuevos.cantidad = nuevaCantidad;
        }
        if(nuevaUnidad !== unidadMedida){
            datosNuevos.unidadMedida = nuevaUnidad;
        }
        if(nuevoStockMinimo !== stockMinimo && nuevoStockMinimo > 0){
            datosNuevos.stockMinimo = nuevoStockMinimo;
        }
        if(Object.keys(datosNuevos).length > 0){
            fetch('actualizarInventarioMP.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codigo: idInventario,
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
                    title: "Inventario actualizado",
                    text: "El inventario se ha actualizado correctamente.",
                    icon: "success",
                    });
                    cargarInventario(sucursalActual);
                }
                else {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo actualizar el inventario.",
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
    const sucursalActual = seleccionSucursal.value;
    const idInventario = fila.getAttribute('data-idInventario');
    const materiaPrima = fila.children[1].textContent;
    Swal.fire({
        title: "Eliminación de Inventario",
        text: "Se eliminará el registro de " + materiaPrima + " del inventario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Confirmar eliminación",
        cancelButtonText: "Cancelar"
    }).then((result) => {
    if (result.isConfirmed) {
        fetch('eliminarInventarioMP.php', {
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
                cargarInventario(sucursalActual);
            }
            else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo eliminar el inventario.",
                    icon: "error",
                });
            }
        });
    }
    });
}

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        if (urlParams.get('status') === 'success') {
            Swal.fire({
                title: "Registro Agregado",
                text: "El registro se ha ingresado al inventario correctamente.",
                icon: "success"
            });
            const sucursalActual = seleccionSucursal.value;
            cargarInventario(sucursalActual);
        } else if (urlParams.get('status') === 'error') {
            Swal.fire({
                title: "Error",
                text: "No se pudo agregar al inventario.",
                icon: "error",
            });
        }
    }
    const url = new URL(window.location);
    url.searchParams.delete('status');
    window.history.replaceState({}, document.title, url);
};