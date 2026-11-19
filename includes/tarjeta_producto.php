<?php
// Renders one product card (used by index.php and productos.php)
function tarjeta_producto(array $p) {
    $img = $p['imagen'] ? BASE_URL . 'assets/img/productos/' . e($p['imagen']) : BASE_URL . 'assets/img/placeholder.svg';
    $hay = (int)$p['stock'] > 0;
    ?>
    <div class="col-sm-6 col-lg-3 producto-item" data-texto="<?= e(strtolower($p['nombre'] . ' ' . $p['categoria'])) ?>">
      <div class="card product-card h-100">
        <img src="<?= $img ?>" class="card-img-top" alt="<?= e($p['nombre']) ?>">
        <div class="card-body d-flex flex-column">
          <span class="badge badge-cat align-self-start mb-2"><?= e($p['categoria']) ?></span>
          <h5 class="card-title h6"><?= e($p['nombre']) ?></h5>
          <p class="small text-secondary flex-grow-1"><?= e($p['descripcion']) ?></p>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <strong class="price"><?= cop($p['precio']) ?></strong>
            <small class="<?= $hay ? 'text-success' : 'text-danger' ?>"><?= $hay ? 'Stock: ' . (int)$p['stock'] : 'Out of stock' ?></small>
          </div>
          <?php if ($hay): ?>
            <button class="btn btn-accent w-100 btn-consultar" data-bs-toggle="modal" data-bs-target="#modalProducto"
              data-id="<?= (int)$p['id'] ?>" data-nombre="<?= e($p['nombre']) ?>" data-stock="<?= (int)$p['stock'] ?>">
              <i class="bi bi-bag"></i> Inquire / Buy</button>
          <?php else: ?>
            <button class="btn btn-secondary w-100" disabled>Out of stock</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
}
