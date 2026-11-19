<footer class="site-footer mt-5 pt-5 pb-3">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-2 mb-2"><img src="<?= BASE_URL ?>assets/img/logo.svg" width="34" alt=""><strong>Auto<span class="text-accent">Care</span></strong></div>
        <p class="text-secondary small">Professional car care products and car wash services for motorcycles, cars and pickup trucks.</p>
      </div>
      <div class="col-md-4">
        <h6>Quick links</h6>
        <ul class="list-unstyled small">
          <li><a href="<?= BASE_URL ?>productos.php">Products</a></li>
          <li><a href="<?= BASE_URL ?>precios.php">Car wash prices</a></li>
          <li><a href="<?= BASE_URL ?>contacto.php">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6>Contact</h6>
        <ul class="list-unstyled small text-secondary">
          <li><i class="bi bi-geo-alt text-accent"></i> <?= e(NEGOCIO_DIR) ?></li>
          <li><i class="bi bi-telephone text-accent"></i> <?= e(NEGOCIO_TEL) ?></li>
          <li><i class="bi bi-envelope text-accent"></i> <?= e(NEGOCIO_EMAIL) ?></li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center text-secondary small mb-0">&copy; <?= date('Y') ?> AutoCare. All rights reserved.</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>assets/js/app.js"></script>
<?php foreach (($extraJs ?? []) as $js): ?><script src="<?= BASE_URL ?>assets/js/<?= e($js) ?>"></script><?php endforeach; ?>
<?php flash_script(); ?>
</body>
</html>
