<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/precios.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($accion === 'eliminar') {
            $pdo->prepare('DELETE FROM precios_lavado WHERE id = ?')->execute([$id]); flash('success', 'Price deleted.');
        } elseif ($accion === 'guardar') {
            $tipo = $_POST['tipo_vehiculo'] ?? ''; $serv = (int)($_POST['servicio_id'] ?? 0); $precio = $_POST['precio'] ?? ''; $err = [];
            if (!isset(VEHICULOS[$tipo])) { $err[] = 'Select a valid vehicle type.'; }
            $c = $pdo->prepare('SELECT COUNT(*) FROM servicios WHERE id = ?'); $c->execute([$serv]);
            if (!$c->fetchColumn()) { $err[] = 'Select a valid service.'; }
            if (!is_numeric($precio) || $precio < 0 || $precio > 99999999) { $err[] = 'Invalid price.'; }
            if ($err) { flash('error', implode(' ', $err)); redirect('admin/precios.php'); }
            if ($id) { $pdo->prepare('UPDATE precios_lavado SET tipo_vehiculo=?, servicio_id=?, precio=? WHERE id=?')->execute([$tipo, $serv, $precio, $id]); flash('success', 'Price updated.'); }
            else { $pdo->prepare('INSERT INTO precios_lavado (tipo_vehiculo, servicio_id, precio) VALUES (?,?,?)')->execute([$tipo, $serv, $precio]); flash('success', 'Price added.'); }
        }
    } catch (PDOException $ex) {
        flash('error', $ex->getCode() === '23000' ? 'That vehicle type and package already has a price. Edit it instead.' : 'Database error.');
    }
    redirect('admin/precios.php');
}
$adminTitle = 'Manage Car Wash Prices';
$servs = $pdo->query('SELECT id, nombre FROM servicios ORDER BY id')->fetchAll();
$lista = $pdo->query('SELECT pl.*, s.nombre AS servicio FROM precios_lavado pl JOIN servicios s ON s.id = pl.servicio_id ORDER BY FIELD(pl.tipo_vehiculo,"moto","carro","camioneta"), pl.precio')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="d-flex justify-content-between mb-3"><span class="text-secondary"><?= count($lista) ?> prices</span>
  <button class="btn btn-accent btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalCrud"><i class="bi bi-plus-lg"></i> Add price</button></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Vehicle</th><th>Package</th><th>Price</th><th class="text-end">Actions</th></tr></thead><tbody>
  <?php foreach ($lista as $r): $json = ['id' => $r['id'], 'tipo_vehiculo' => $r['tipo_vehiculo'], 'servicio_id' => $r['servicio_id'], 'precio' => $r['precio']]; ?>
    <tr><td><?= e(VEHICULOS[$r['tipo_vehiculo']]) ?></td><td><?= e($r['servicio']) ?></td><td class="price"><?= cop($r['precio']) ?></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-info btn-editar" data-bs-toggle="modal" data-bs-target="#modalCrud" data-json="<?= e(json_encode($json)) ?>"><i class="bi bi-pencil"></i></button>
        <form method="post" class="d-inline form-eliminar" data-msg="Delete this price?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
  <?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="modalCrud" tabindex="-1"><div class="modal-dialog modal-dialog-centered">
  <form class="modal-content needs-validation" method="post" novalidate>
    <?= csrf_field() ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id">
    <div class="modal-header"><h5 class="modal-title" data-base="Price">Price</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body vstack gap-3">
      <div><label class="form-label">Vehicle type</label><select name="tipo_vehiculo" class="form-select" required><option value="">Select...</option><?php foreach (VEHICULOS as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?></select><div class="invalid-feedback">Select a vehicle type.</div></div>
      <div><label class="form-label">Wash package</label><select name="servicio_id" class="form-select" required><option value="">Select...</option><?php foreach ($servs as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['nombre']) ?></option><?php endforeach; ?></select><div class="invalid-feedback">Select a package.</div></div>
      <div><label class="form-label">Price (COP)</label><input type="number" name="precio" class="form-control" min="0" step="500" required><div class="invalid-feedback">Enter a valid price.</div></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Save</button></div>
  </form></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
