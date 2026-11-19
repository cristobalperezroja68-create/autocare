document.addEventListener('DOMContentLoaded', () => {
  const swalBase = { background: '#1b1b22', color: '#fff', confirmButtonColor: '#7c4dff' };

  // Bootstrap form validation (+ password confirmation)
  document.querySelectorAll('form.needs-validation').forEach(form => {
    form.addEventListener('submit', ev => {
      const p = form.querySelector('[name=password]'), c = form.querySelector('[name=password2]');
      if (p && c) { c.setCustomValidity(p.value !== c.value ? 'Passwords do not match' : ''); }
      if (!form.checkValidity()) { ev.preventDefault(); ev.stopPropagation(); }
      form.classList.add('was-validated');
    });
  });

  // SweetAlert2 confirmation before deleting
  document.querySelectorAll('form.form-eliminar').forEach(form => {
    form.addEventListener('submit', ev => {
      ev.preventDefault();
      Swal.fire({ ...swalBase, title: 'Are you sure?', text: form.dataset.msg || 'This action cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel', confirmButtonColor: '#dc3545' })
        .then(r => { if (r.isConfirmed) form.submit(); });
    });
  });

  // Auto-submit selects (request status)
  document.querySelectorAll('select.auto-submit').forEach(s => s.addEventListener('change', () => s.form.submit()));

  // Generic add/edit modal for the admin CRUD pages
  const crud = document.getElementById('modalCrud');
  if (crud) {
    const form = crud.querySelector('form'), title = crud.querySelector('.modal-title'), base = title.dataset.base || '';
    const reset = () => { form.reset(); form.classList.remove('was-validated'); };
    document.querySelectorAll('.btn-nuevo').forEach(b => b.addEventListener('click', () => {
      reset(); if (form.elements.id) form.elements.id.value = ''; title.textContent = 'Add ' + base;
      if (form.elements.password) form.elements.password.required = true;
    }));
    document.querySelectorAll('.btn-editar').forEach(b => b.addEventListener('click', () => {
      reset(); const d = JSON.parse(b.dataset.json);
      Object.keys(d).forEach(k => { const el = form.elements[k]; if (el && el.type !== 'file') el.value = d[k] ?? ''; });
      title.textContent = 'Edit ' + base;
      if (form.elements.password) form.elements.password.required = false;
    }));
  }

  // Car wash request modal
  document.querySelectorAll('.btn-servicio').forEach(b => b.addEventListener('click', () => {
    document.getElementById('servPrecioId').value = b.dataset.id;
    document.getElementById('servEtiqueta').textContent = b.dataset.etiqueta;
  }));
});
