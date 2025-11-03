function agregarDetalle() {
  const container = document.getElementById('detalle-container');
  const original = container.querySelector('.detalle-item');
  const clone = original.cloneNode(true);

  clone.querySelectorAll('select, input').forEach(el => el.value = '');
  container.appendChild(clone);
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('select-plato')) {
    const idPlato = e.target.value;
    const precioInput = e.target.closest('.detalle-item').querySelector('.precio-unitario');

    if (idPlato) {
      fetch(`get_precio_plato.php?id=${idPlato}`)
        .then(res => res.text())
        .then(precio => {
          precioInput.value = precio;
          calcularSubtotal(precioInput);
        });
    } else {
      precioInput.value = '';
      calcularSubtotal(precioInput);
    }
  }

  if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio-unitario')) {
    calcularSubtotal(e.target);
  }
});

function calcularSubtotal(element) {
  const item = element.closest('.detalle-item');
  const cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
  const precio = parseFloat(item.querySelector('.precio-unitario').value) || 0;
  const subtotal = cantidad * precio;
  item.querySelector('.subtotal').value = subtotal.toFixed(2);
  calcularTotal();
}

function calcularTotal() {
  let total = 0;
  document.querySelectorAll('.subtotal').forEach(input => {
    total += parseFloat(input.value) || 0;
  });
  document.getElementById('total_venta').value = total.toFixed(2);
}

