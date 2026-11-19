document.addEventListener('DOMContentLoaded', () => {
  // Fill the inquiry modal with the selected product
  document.querySelectorAll('.btn-consultar').forEach(b => b.addEventListener('click', () => {
    document.getElementById('prodId').value = b.dataset.id;
    document.getElementById('prodNombre').textContent = b.dataset.nombre;
    const q = document.getElementById('prodCantidad');
    q.value = 1; q.max = b.dataset.stock;
  }));

  // Live search in the catalog
  const buscador = document.getElementById('buscador');
  if (buscador) {
    buscador.addEventListener('input', () => {
      const t = buscador.value.trim().toLowerCase(); let visibles = 0;
      document.querySelectorAll('.producto-item').forEach(el => {
        const ok = el.dataset.texto.includes(t); el.classList.toggle('d-none', !ok); if (ok) visibles++;
      });
      document.getElementById('sinResultados').classList.toggle('d-none', visibles > 0);
    });
  }
});
