document.addEventListener("DOMContentLoaded", () => {
  const tablaRecetas = document.getElementById("tablaRecetas");
  const tablaInsumos = document.querySelector("#tablaInsumos tbody");
  const btnAgregarInsumo = document.getElementById("btnAgregarInsumo");
  const formReceta = document.getElementById("formReceta");

  let detalle = [];

  // 🧾 Cargar recetas
  const cargarRecetas = () => {
    fetch("recetas_crud.php", {
      method: "POST",
      body: new URLSearchParams({ accion: "listar" }),
    })
      .then((res) => res.json())
      .then((data) => {
        tablaRecetas.innerHTML = "";
        data.forEach((r) => {
          const det = r.detalle
            .map((d) => `${d.descripcion} (${d.cantidad})`)
            .join(", ");
          tablaRecetas.innerHTML += `
            <tr>
              <td>${r.id_receta}</td>
              <td>${r.nombre_plato}</td>
              <td>${det}</td>
              <td>
                <button onclick="eliminarReceta(${r.id_receta})">Eliminar</button>
              </td>
            </tr>`;
        });
      });
  };

  // ➕ Agregar insumo a la tabla local
  btnAgregarInsumo.addEventListener("click", () => {
    const id = document.getElementById("id_insumo").value;
    const nombre = document.getElementById("id_insumo").selectedOptions[0].text;
    const cantidad = parseFloat(document.getElementById("cantidad").value);

    if (!id || isNaN(cantidad) || cantidad <= 0) {
      alert("Seleccione un insumo y cantidad válida");
      return;
    }

    detalle.push({ id_insumo: id, cantidad });

    const fila = document.createElement("tr");
    fila.innerHTML = `
      <td>${nombre}</td>
      <td>${cantidad}</td>
      <td><button type="button" class="btnEliminarInsumo">Eliminar</button></td>`;
    tablaInsumos.appendChild(fila);

    fila.querySelector(".btnEliminarInsumo").addEventListener("click", () => {
      fila.remove();
      detalle = detalle.filter((d) => d.id_insumo != id);
    });

    document.getElementById("cantidad").value = "";
    document.getElementById("id_insumo").value = "";
  });

  // 💾 Guardar receta
  formReceta.addEventListener("submit", (e) => {
    e.preventDefault();
    const id_plato = document.getElementById("id_plato").value;

    if (!id_plato || detalle.length === 0) {
      alert("Seleccione un plato y al menos un ingrediente");
      return;
    }

    const data = new URLSearchParams();
    data.append("accion", "agregar");
    data.append("id_plato", id_plato);
    data.append("detalle", JSON.stringify(detalle));

    fetch("recetas_crud.php", { method: "POST", body: data })
      .then((res) => res.text())
      .then((r) => {
        if (r === "ok") {
          alert("Receta guardada correctamente");
          formReceta.reset();
          detalle = [];
          tablaInsumos.innerHTML = "";
          cargarRecetas();
        }
      });
  });

  // ❌ Eliminar receta
  window.eliminarReceta = (id) => {
    if (!confirm("¿Eliminar esta receta?")) return;
    fetch("recetas_crud.php", {
      method: "POST",
      body: new URLSearchParams({ accion: "eliminar", id_receta: id }),
    })
      .then((res) => res.text())
      .then((r) => {
        if (r === "ok") {
          alert("Receta eliminada");
          cargarRecetas();
        }
      });
  };

  cargarRecetas();
});
