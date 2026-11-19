<?php
$cur = basename($_SERVER['SCRIPT_NAME']);
$u = $_SESSION['user'] ?? null;
$items = ['index.php' => 'Home', 'productos.php' => 'Products', 'servicios.php' => 'Car Wash Services',
          'precios.php' => 'Prices', 'nosotros.php' => 'About Us', 'contacto.php' => 'Contact'];
?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>index.php">
      <img src="<?= BASE_URL ?>assets/img/logo.svg" width="38" height="38" alt="AutoCare logo">
      <span class="fw-bold">Auto<span class="text-accent">Care</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Menu"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <?php foreach ($items as $file => $label): ?>
          <li class="nav-item"><a class="nav-link <?= $cur === $file ? 'active' : '' ?>" href="<?= BASE_URL . $file ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
        <?php if ($u): ?>
          <?php if ($u['rol'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/dashboard.php"><i class="bi bi-gear"></i> Admin</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="btn btn-outline-accent btn-sm ms-lg-2" href="<?= BASE_URL ?>logout.php">Logout (<?= e(explode(' ', $u['nombre'])[0]) ?>)</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-accent btn-sm ms-lg-2 px-3" href="<?= BASE_URL ?>login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
