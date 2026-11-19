<?php
require_once __DIR__ . '/verificar_sesion.php';
require_admin();
$cur = basename($_SERVER['SCRIPT_NAME']);
$menu = ['dashboard.php' => ['bi-speedometer2', 'Dashboard'], 'productos.php' => ['bi-box-seam', 'Manage Products'],
  'categorias.php' => ['bi-tags', 'Manage Categories'], 'precios.php' => ['bi-cash-coin', 'Manage Car Wash Prices'],
  'servicios.php' => ['bi-droplet-half', 'Manage Services'], 'usuarios.php' => ['bi-people', 'Manage Users'],
  'solicitudes.php' => ['bi-inbox', 'Requests']];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($adminTitle ?? 'Admin') ?> | AutoCare Admin</title>
<link rel="icon" href="<?= BASE_URL ?>assets/img/logo.svg">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/estilos.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin">
<div class="d-flex">
  <aside class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebar">
    <div class="offcanvas-header"><span class="fw-bold">Menu</span><button class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button></div>
    <div class="offcanvas-body d-flex flex-column p-0">
      <a href="<?= BASE_URL ?>index.php" class="sidebar-brand d-flex align-items-center gap-2 p-3 text-decoration-none">
        <img src="<?= BASE_URL ?>assets/img/logo.svg" width="36" alt=""><span class="fw-bold text-white">Auto<span class="text-accent">Care</span> Admin</span></a>
      <nav class="nav flex-column px-2 flex-grow-1">
        <?php foreach ($menu as $file => [$icon, $label]): ?>
          <a class="nav-link <?= $cur === $file ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/<?= $file ?>"><i class="bi <?= $icon ?>"></i> <?= $label ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="px-2 pb-3">
        <a class="nav-link" href="<?= BASE_URL ?>index.php"><i class="bi bi-globe"></i> View website</a>
        <a class="nav-link text-danger" href="<?= BASE_URL ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>
    </div>
  </aside>
  <main class="flex-grow-1 p-3 p-lg-4">
    <div class="d-flex align-items-center gap-2 mb-4">
      <button class="btn btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar"><i class="bi bi-list"></i></button>
      <h1 class="h3 mb-0"><?= e($adminTitle ?? '') ?></h1>
      <span class="ms-auto text-secondary small d-none d-md-inline"><i class="bi bi-person-circle"></i> <?= e($_SESSION['user']['nombre']) ?></span>
    </div>
