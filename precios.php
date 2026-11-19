<?php
$pageTitle = 'Our Car Wash Prices | AutoCare';
require_once __DIR__ . '/includes/header.php';
$rows = $pdo->query('SELECT pl.id, pl.tipo_vehiculo, pl.precio, s.nombre, s.descripcion FROM precios_lavado pl JOIN servicios s ON s.id = pl.servicio_id WHERE s.activo = 1 ORDER BY pl.precio')->fetchAll();
$porTipo = [];
foreach ($rows as $r) { $porTipo[$r['tipo_vehiculo']][] = $r; }
$iconos = ['moto' => 'bi-speedometer2', 'carro' => 'bi-car-front-fill', 'camioneta' => 'bi-truck'];
?>
<div class="page-head"><div class="container"><h1>Our Car Wash Prices</h1><p class="text-secondary mb-0">Transparent prices in Colombian pesos (COP).</p></div></div>
<div class="container py-4">
  <div class="row g-4">
    <?php foreach (VEHICULOS as $tipo => $nombreTipo): ?>
      <div class="col-lg-4"><div class="card h-100 price-card">
        <div class="card-header text-center py-3"><i class="bi <?= $iconos[$tipo] ?> fs-1 text-accent"></i><h4 class="mb-0 mt-1"><?= e($nombreTipo) ?> Wash</h4></div>
        <div class="card-body vstack gap-3">
          <?php foreach ($porTipo[$tipo] ?? [] as $r): ?>
            <div class="paquete p-3">
              <div class="d-flex justify-content-between"><strong><?= e($r['nombre']) ?></strong><span class="price fw-bold"><?= cop($r['precio']) ?></span></div>
              <p class="small text-secondary my-2"><?= e($r['descripcion']) ?></p>
              <button class="btn btn-accent btn-sm w-100 btn-servicio" data-bs-toggle="modal" data-bs-target="#modalServicio"
                data-id="<?= (int)$r['id'] ?>" data-etiqueta="<?= e($nombreTipo . ' - ' . $r['nombre'] . ' (' . cop($r['precio']) . ')') ?>">Request service</button>
            </div>
          <?php endforeach; ?>
          <?php if (empty($porTipo[$tipo])): ?><p class="text-secondary text-center">No prices available.</p><?php endif; ?>
        </div>
      </div></div>
    <?php endforeach; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/modal_servicio.php'; include __DIR__ . '/includes/footer.php'; ?>
