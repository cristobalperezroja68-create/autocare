<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/solicitudes.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'eliminar') { $pdo->prepare('DELETE FROM solicitudes WHERE id = ?')->execute([$id]); flash('success', 'Request deleted.'); }
    elseif ($accion === 'estado' && in_array($_POST['estado'] ?? '', ['pendiente', 'atendida', 'cancelada'], true)) {
        $pdo->prepare('UPDATE solicitudes SET estado = ? WHERE id = ?')->execute([$_POST['estado'], $id]); flash('success', 'Status updated.');
    }
    redirect('admin/solicitudes.php');
}
$adminTitle = 'Requests';
$lista = $pdo->query('SELECT * FROM solicitudes ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Date</th><th>Type</th><th>Customer</th><th>Detail</th><th>Status</th><th class="text-end">Delete</th></tr></thead><tbody>
  <?php foreach ($lista as $s): ?>
    <tr><td class="small text-secondary"><?= e(date('Y-m-d H:i', strtotime($s['creado_en']))) ?></td><td><span class="badge text-bg-secondary"><?= e($s['tipo']) ?></span></td>
      <td><?= e($s['nombre']) ?><br><small class="text-secondary"><?= e($s['telefono']) ?><?= $s['email'] ? ' · ' . e($s['email']) : '' ?></small></td>
      <td><?= e($s['referencia']) ?><?= $s['tipo'] === 'producto' ? ' x' . (int)$s['cantidad'] : '' ?><?= $s['mensaje'] ? '<br><small class="text-secondary">' . e($s['mensaje']) . '</small>' : '' ?></td>
      <td><form method="post"><?= csrf_field() ?><input type="hidden" name="accion" value="estado"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
        <select name="estado" class="form-select form-select-sm auto-submit"><?php foreach (['pendiente' => 'Pending', 'atendida' => 'Attended', 'cancelada' => 'Cancelled'] as $k => $v): ?><option value="<?= $k ?>" <?= $s['estado'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></form></td>
      <td class="text-end"><form method="post" class="d-inline form-eliminar" data-msg="Delete this request?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
  <?php endforeach; ?>
  <?php if (!$lista): ?><tr><td colspan="6" class="text-center text-secondary py-4">No requests yet.</td></tr><?php endif; ?></tbody></table></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
