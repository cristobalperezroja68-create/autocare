<?php
$pageTitle = 'Car Wash Services | AutoCare';
require_once __DIR__ . '/includes/header.php';
$servicios = $pdo->query('SELECT * FROM servicios WHERE activo = 1 ORDER BY id')->fetchAll();
$precios = [];
foreach ($pdo->query('SELECT * FROM precios_lavado ORDER BY FIELD(tipo_vehiculo,"moto","carro","camioneta")') as $r) { $precios[$r['servicio_id']][] = $r; }
?>
<div class="page-head"><div class="container"><h1>Car wash services</h1><p class="text-secondary mb-0">Choose the package that fits your vehicle.</p></div></div>
<div class="container py-4">
  <div class="row g-4">
    <?php foreach ($servicios as $s): ?>
      <div class="col-md-4"><div class="card h-100 p-2">
        <div class="card-body">
          <h4><i class="bi bi-stars text-accent"></i> <?= e($s['nombre']) ?></h4>
          <p class="text-secondary"><?= e($s['descripcion']) ?></p>
          <ul class="list-unstyled">
            <?php foreach ($precios[$s['id']] ?? [] as $r): ?>
              <li class="d-flex justify-content-between align-items-center border-top border-secondary-subtle py-2">
                <span><?= e(VEHICULOS[$r['tipo_vehiculo']]) ?><br><strong class="price"><?= cop($r['precio']) ?></strong></span>
                <button class="btn btn-accent btn-sm btn-servicio" data-bs-toggle="modal" data-bs-target="#modalServicio"
                  data-id="<?= (int)$r['id'] ?>" data-etiqueta="<?= e(VEHICULOS[$r['tipo_vehiculo']] . ' - ' . $s['nombre'] . ' (' . cop($r['precio']) . ')') ?>">Request</button>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div></div>
    <?php endforeach; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/modal_servicio.php'; include __DIR__ . '/includes/footer.php'; ?>
