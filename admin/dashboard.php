<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
$adminTitle = 'Dashboard';
$n = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$totProd = $n('SELECT COUNT(*) FROM productos');
$totUsers = $n('SELECT COUNT(*) FROM usuarios');
$totServ = $n('SELECT COUNT(*) FROM servicios');
$disp = $n('SELECT COUNT(*) FROM productos WHERE stock > 0');
$valor = (float)$pdo->query('SELECT COALESCE(SUM(precio * stock),0) FROM productos')->fetchColumn();
$pend = $n("SELECT COUNT(*) FROM solicitudes WHERE estado = 'pendiente'");
$totCat = $n('SELECT COUNT(*) FROM categorias');
$bajos = $pdo->query('SELECT nombre, stock FROM productos WHERE stock <= 5 ORDER BY stock, nombre LIMIT 6')->fetchAll();
$ultimas = $pdo->query('SELECT * FROM solicitudes ORDER BY id DESC LIMIT 6')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
$cards = [['Total products', $totProd, 'bi-box-seam'], ['Total users', $totUsers, 'bi-people'], ['Total services', $totServ, 'bi-droplet-half'], ['Available products', $disp, 'bi-check2-circle']];
?>
<div class="row g-3 mb-4">
  <?php foreach ($cards as [$l, $v, $i]): ?>
    <div class="col-sm-6 col-xl-3"><div class="card stat p-3"><div class="d-flex justify-content-between align-items-center">
      <div><div class="text-secondary small"><?= $l ?></div><div class="display-6 fw-bold"><?= $v ?></div></div><i class="bi <?= $i ?> fs-1 text-accent"></i></div></div></div>
  <?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Inventory value</div><div class="h4 mb-0 price"><?= cop($valor) ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Pending requests</div><div class="h4 mb-0"><?= $pend ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Categories</div><div class="h4 mb-0"><?= $totCat ?></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-6"><div class="card p-3 h-100"><h5>Low stock (5 or less)</h5>
    <?php if (!$bajos): ?><p class="text-secondary mb-0">All products have enough stock.</p><?php endif; ?>
    <ul class="list-group list-group-flush"><?php foreach ($bajos as $b): ?>
      <li class="list-group-item d-flex justify-content-between bg-transparent"><?= e($b['nombre']) ?><span class="badge <?= $b['stock'] ? 'text-bg-warning' : 'text-bg-danger' ?>"><?= (int)$b['stock'] ?></span></li>
    <?php endforeach; ?></ul></div></div>
  <div class="col-lg-6"><div class="card p-3 h-100"><h5>Latest requests</h5>
    <?php if (!$ultimas): ?><p class="text-secondary mb-0">No requests yet.</p><?php endif; ?>
    <ul class="list-group list-group-flush"><?php foreach ($ultimas as $s): ?>
      <li class="list-group-item bg-transparent"><strong><?= e($s['nombre']) ?></strong> <span class="badge text-bg-secondary"><?= e($s['tipo']) ?></span><br><small class="text-secondary"><?= e($s['referencia']) ?> &middot; <?= e($s['estado']) ?></small></li>
    <?php endforeach; ?></ul>
    <a href="<?= BASE_URL ?>admin/solicitudes.php" class="btn btn-outline-accent btn-sm mt-2 align-self-start">See all</a></div></div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
