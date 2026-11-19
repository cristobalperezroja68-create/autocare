<?php
$pageTitle = 'Products | AutoCare';
$extraJs = ['productos.js'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/tarjeta_producto.php';
$cat = (int)($_GET['cat'] ?? 0);
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
$sql = 'SELECT p.*, c.nombre AS categoria FROM productos p JOIN categorias c ON c.id = p.categoria_id';
$params = [];
if ($cat > 0) { $sql .= ' WHERE p.categoria_id = ?'; $params[] = $cat; }
$st = $pdo->prepare($sql . ' ORDER BY p.nombre');
$st->execute($params);
$productos = $st->fetchAll();
?>
<div class="page-head"><div class="container"><h1>Product catalog</h1><p class="text-secondary mb-0">Specialized products for cleaning, caring for and protecting your vehicle.</p></div></div>
<div class="container py-4">
  <div class="row g-3 mb-4">
    <div class="col-lg-8 d-flex flex-wrap gap-2">
      <a href="<?= BASE_URL ?>productos.php" class="btn btn-sm <?= !$cat ? 'btn-accent' : 'btn-outline-accent' ?>">All</a>
      <?php foreach ($categorias as $c): ?>
        <a href="<?= BASE_URL ?>productos.php?cat=<?= (int)$c['id'] ?>" class="btn btn-sm <?= $cat === (int)$c['id'] ? 'btn-accent' : 'btn-outline-accent' ?>"><?= e($c['nombre']) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="col-lg-4"><input type="search" id="buscador" class="form-control" placeholder="Search products..."></div>
  </div>
  <div class="row g-4" id="listaProductos">
    <?php foreach ($productos as $p) { tarjeta_producto($p); } ?>
  </div>
  <p id="sinResultados" class="text-center text-secondary py-5 <?= $productos ? 'd-none' : '' ?>">No products found.</p>
</div>
<?php include __DIR__ . '/includes/modal_producto.php'; include __DIR__ . '/includes/footer.php'; ?>
