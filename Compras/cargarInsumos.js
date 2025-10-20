window.Compras = window.Compras || {};
(function(NS){
  if (NS._insumosLoaded) return;
  NS._insumosLoaded = true;

  let insumoOptionsHTML = null; 

  async function fetchInsumoOptions() {
    const res = await fetch('cargarInsumos.php?format=options', { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (insumos):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  // Llena todos los selects de insumo existentes
  NS.cargarInsumosEnPagina = async function cargarInsumosEnPagina() {
    const selects = document.querySelectorAll('select.sel-insumo'); 
    if (!selects.length) return;
    try {
      if (!insumoOptionsHTML) insumoOptionsHTML = await fetchInsumoOptions();
      selects.forEach(sel => sel.innerHTML = insumoOptionsHTML);
    } catch (e) {
      console.error('Error cargarInsumosEnPagina:', e);
      selects.forEach(sel => sel.innerHTML = '<option value="">Error cargando insumos</option>');
    }
  };

  // Llena el select de una fila nueva 
  NS.hydrateRowInsumos = async function hydrateRowInsumos(row) {
    const sel = row.querySelector('select.sel-insumo');
    if (!sel) return;
    try {
      if (!insumoOptionsHTML) insumoOptionsHTML = await fetchInsumoOptions();
      sel.innerHTML = insumoOptionsHTML;
    } catch (e) {
      console.error('Error hydrateRowInsumos:', e);
      sel.innerHTML = '<option value="">Error cargando insumos</option>';
    }
  };

  document.addEventListener('DOMContentLoaded', NS.cargarInsumosEnPagina);
})(window.Compras);
