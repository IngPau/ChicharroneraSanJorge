window.Compras = window.Compras || {};
(function(NS){
  if (NS._InsumosLoaded) return;
  NS._InsumosLoaded = true;

  const PROV_URL = 'cargarInsumos.php?format=options';
  let InsumosOptionsHTML = null;

  async function fetchOptions(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (insumos):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  NS.ensureInsumosOptions = async function() {
    if (!InsumosOptionsHTML) {
      InsumosOptionsHTML = await fetchOptions(PROV_URL);
    }
    return InsumosOptionsHTML;
  };

  NS.fillInsumosSelect = async function(selectEl) {
    try {
      const html = await NS.ensureInsumosOptions();
      selectEl.innerHTML = html;
    } catch (e) {
      console.error('Error cargando proveedores:', e);
      selectEl.innerHTML = '<option value="">Error cargando Insumos</option>';
    }
  };

  NS.fillAllInsumosSelects = async function() {
    const html = await NS.ensureInsumosOptions();
    document.querySelectorAll('select.sel-insumo').forEach(sel => {
      sel.innerHTML = html;
    });
  };

  document.addEventListener('DOMContentLoaded', NS.fillAllInsumosSelects);
})(window.Compras);
