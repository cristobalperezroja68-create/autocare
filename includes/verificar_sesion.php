<?php
// includes/verificar_sesion.php - access control helpers
require_once __DIR__ . '/../config/conexion.php';

function require_login() {
    if (empty($_SESSION['user'])) { flash('warning', 'Please log in to continue.'); redirect('login.php'); }
}
function require_admin() {
    global $pdo;
    require_login();
    // Re-check the role in the database on every admin request
    $st = $pdo->prepare('SELECT rol FROM usuarios WHERE id = ?');
    $st->execute([$_SESSION['user']['id']]);
    if ($st->fetchColumn() !== 'admin') { flash('error', 'Access denied: administrators only.'); redirect('index.php'); }
}
