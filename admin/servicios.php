<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/servicios.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'eliminar') {
        $pdo->prepare('DELETE FROM servicios WHERE id = ?')->execute([$id]); flash('success', 'Service deleted (its prices were removed too).');
    } elseif ($accion === 'guardar') {
        $nombre = trim($_POST['nombre'] ?? ''); $desc = trim($_POST['descripcion'] ?? ''); $activo = ($_POST['activo'] ?? '1') === '1' ? 1 : 0;
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100 || mb_strlen($desc) > 1000) { flash('error', 'Name must be 2-100 characters; description up to 1000.'); redirect('admin/servicios.php'); }
        if ($id) { $pdo->prepare('UPDATE servicios SET nombre=?, descripcion=?, activo=? WHERE id=?')->execute([$nombre, $desc, $activo, $id]); flash('success', 'Service updated.'); }
        else { $pdo->prepare('INSERT INTO servicios (nombre, descripcion, activo) VALUES (?,?,?)')->execute([$nombre, $desc, $activo]); flash('success', 'Service added.'); }
    }
    redirect('admin/servicios.php');
}
$adminTitle = 'Manage Services';
$lista = $pdo->query('SELECT * FROM servicios ORDER BY id')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="d-flex justify-content-between mb-3"><span class="text-secondary"><?= count($lista) ?> services</span>
  <button class="btn btn-accent btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalCrud"><i class="bi bi-plus-lg"></i> Add service</button></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Name</th><th>Included services</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
  <?php foreach ($lista as $s): $json = ['id' => $s['id'], 'nombre' => $s['nombre'], 'descripcion' => $s['descripcion'], 'activo' => $s['activo']]; ?>
    <tr><td><?= e($s['nombre']) ?></td><td class="text-secondary"><?= e($s['descripcion']) ?></td>
      <td><span class="badge <?= $s['activo'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $s['activo'] ? 'Active' : 'Hidden' ?></span></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-info btn-editar" data-bs-toggle="modal" data-bs-target="#modalCrud" data-json="<?= e(json_encode($json)) ?>"><i class="bi bi-pencil"></i></button>
        <form method="post" class="d-inline form-eliminar" data-msg="Delete &quot;<?= e($s['nombre']) ?>&quot; and all its prices?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
  <?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="modalCrud" tabindex="-1"><div class="modal-dialog modal-dialog-centered">
  <form class="modal-content needs-validation" method="post" novalidate>
    <?= csrf_field() ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id">
    <div class="modal-header"><h5 class="modal-title" data-base="Service">Service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body vstack gap-3">
      <div><label class="form-label">Package name</label><input name="nombre" class="form-control" required minlength="2" maxlength="100"><div class="invalid-feedback">Required (2-100 characters).</div></div>
      <div><label class="form-label">Included services</label><textarea name="descripcion" class="form-control" rows="4" maxlength="1000"></textarea></div>
      <div><label class="form-label">Status</label><select name="activo" class="form-select"><option value="1">Active</option><option value="0">Hidden</option></select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Save</button></div>
  </form></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
