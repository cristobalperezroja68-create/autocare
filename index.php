<?php
$pageTitle = 'AutoCare | Car care products & car wash';
$extraJs = ['productos.js'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/tarjeta_producto.php';
$destacados = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p JOIN categorias c ON c.id = p.categoria_id WHERE p.stock > 0 ORDER BY p.id DESC LIMIT 4')->fetchAll();
$servicios = $pdo->query('SELECT s.*, MIN(pl.precio) AS desde FROM servicios s LEFT JOIN precios_lavado pl ON pl.servicio_id = s.id WHERE s.activo = 1 GROUP BY s.id ORDER BY desde LIMIT 3')->fetchAll();
?>
<header class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <img src="<?= BASE_URL ?>assets/img/logo.svg" width="64" class="mb-3" alt="AutoCare logo">
        <h1 class="display-4 fw-bold">Auto<span class="text-accent">Care</span></h1>
        <p class="lead">Welcome! Premium cleaning products and professional car wash for your motorcycle, car or pickup truck.</p>
        <p class="text-secondary">We combine specialized products and expert hands to keep your vehicle clean, protected and looking brand new, inside and out.</p>
        <div class="d-flex flex-wrap gap-2">
          <a href="<?= BASE_URL ?>productos.php" class="btn btn-accent btn-lg"><i class="bi bi-bag"></i> View products</a>
          <a href="<?= BASE_URL ?>precios.php" class="btn btn-outline-accent btn-lg"><i class="bi bi-cash-coin"></i> Car wash prices</a>
        </div>
      </div>
      <div class="col-lg-6 text-center"><img src="<?= BASE_URL ?>assets/img/hero.svg" class="img-fluid" alt="Car being washed"></div>
    </div>
  </div>
</header>

<section class="container py-5">
  <h2 class="section-title">Featured products</h2>
  <div class="row g-4">
    <?php foreach ($destacados as $p) { tarjeta_producto($p); } ?>
    <?php if (!$destacados): ?><p class="text-secondary">No products yet.</p><?php endif; ?>
  </div>
  <div class="text-center mt-4"><a href="<?= BASE_URL ?>productos.php" class="btn btn-outline-accent">See full catalog</a></div>
</section>

<section class="container py-4">
  <h2 class="section-title">Most popular services</h2>
  <div class="row g-4">
    <?php foreach ($servicios as $s): ?>
      <div class="col-md-4"><div class="card h-100 p-3">
        <div class="card-body">
          <i class="bi bi-droplet-half fs-1 text-accent"></i>
          <h5 class="mt-2"><?= e($s['nombre']) ?></h5>
          <p class="text-secondary small"><?= e($s['descripcion']) ?></p>
          <?php if ($s['desde'] !== null): ?><p class="mb-0">From <strong class="price"><?= cop($s['desde']) ?></strong></p><?php endif; ?>
        </div>
      </div></div>
    <?php endforeach; ?>
  </div>
</section>

<section class="container py-5">
  <h2 class="section-title">Contact information</h2>
  <div class="row g-3 text-center">
    <div class="col-md-3"><div class="card p-3 h-100"><i class="bi bi-geo-alt fs-2 text-accent"></i><p class="mb-0 small"><?= e(NEGOCIO_DIR) ?></p></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><i class="bi bi-telephone fs-2 text-accent"></i><p class="mb-0 small"><?= e(NEGOCIO_TEL) ?></p></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><i class="bi bi-envelope fs-2 text-accent"></i><p class="mb-0 small"><?= e(NEGOCIO_EMAIL) ?></p></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><i class="bi bi-clock fs-2 text-accent"></i><p class="mb-0 small"><?= e(NEGOCIO_HORARIO) ?></p></div></div>
  </div>
</section>
<?php include __DIR__ . '/includes/modal_producto.php'; include __DIR__ . '/includes/footer.php'; ?>
