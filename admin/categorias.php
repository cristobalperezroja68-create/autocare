<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/categorias.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($accion === 'eliminar') {
            $pdo->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]); flash('success', 'Category deleted.');
        } elseif ($accion === 'guardar') {
            $nombre = trim($_POST['nombre'] ?? ''); $desc = trim($_POST['descripcion'] ?? '');
            if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100 || mb_strlen($desc) > 255) { flash('error', 'Name must be 2-100 characters; description up to 255.'); redirect('admin/categorias.php'); }
            if ($id) { $pdo->prepare('UPDATE categorias SET nombre=?, descripcion=? WHERE id=?')->execute([$nombre, $desc, $id]); flash('success', 'Category updated.'); }
            else { $pdo->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?,?)')->execute([$nombre, $desc]); flash('success', 'Category added.'); }
        }
    } catch (PDOException $ex) {
        flash('error', $ex->getCode() === '23000' ? 'Cannot complete: the name already exists or the category still has products.' : 'Database error.');
    }
    redirect('admin/categorias.php');
}
$adminTitle = 'Manage Categories';
$lista = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM productos p WHERE p.categoria_id = c.id) AS total FROM categorias c ORDER BY c.nombre')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="d-flex justify-content-between mb-3"><span class="text-secondary"><?= count($lista) ?> categories</span>
  <button class="btn btn-accent btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalCrud"><i class="bi bi-plus-lg"></i> Add category</button></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Name</th><th>Description</th><th>Products</th><th class="text-end">Actions</th></tr></thead><tbody>
  <?php foreach ($lista as $c): $json = ['id' => $c['id'], 'nombre' => $c['nombre'], 'descripcion' => $c['descripcion']]; ?>
    <tr><td><?= e($c['nombre']) ?></td><td class="text-secondary"><?= e($c['descripcion']) ?></td><td><?= (int)$c['total'] ?></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-info btn-editar" data-bs-toggle="modal" data-bs-target="#modalCrud" data-json="<?= e(json_encode($json)) ?>"><i class="bi bi-pencil"></i></button>
        <form method="post" class="d-inline form-eliminar" data-msg="Delete category &quot;<?= e($c['nombre']) ?>&quot;?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
  <?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="modalCrud" tabindex="-1"><div class="modal-dialog modal-dialog-centered">
  <form class="modal-content needs-validation" method="post" novalidate>
    <?= csrf_field() ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id">
    <div class="modal-header"><h5 class="modal-title" data-base="Category">Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body vstack gap-3">
      <div><label class="form-label">Name</label><input name="nombre" class="form-control" required minlength="2" maxlength="100"><div class="invalid-feedback">Required (2-100 characters).</div></div>
      <div><label class="form-label">Description</label><input name="descripcion" class="form-control" maxlength="255"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Save</button></div>
  </form></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
