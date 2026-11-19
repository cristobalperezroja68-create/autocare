<?php
// Handles product inquiries, service requests and contact messages
require_once __DIR__ . '/config/conexion.php';
if (!is_post() || !csrf_ok()) { flash('error', 'Invalid request. Please try again.'); redirect('index.php'); }

$tipo = $_POST['tipo'] ?? '';
$back = ['producto' => 'productos.php', 'servicio' => 'precios.php', 'contacto' => 'contacto.php'][$tipo] ?? 'index.php';
$nombre = trim($_POST['nombre'] ?? ''); $telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? ''); $mensaje = trim($_POST['mensaje'] ?? '');
$err = []; $ref = ''; $cantidad = 1;

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) { $err[] = 'Enter your name (3-100 characters).'; }
if (!preg_match('/^[0-9+\s()\-]{7,20}$/', $telefono)) { $err[] = 'Enter a valid phone number.'; }
if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120)) { $err[] = 'Invalid email address.'; }
if (mb_strlen($mensaje) > 500) { $err[] = 'Message is too long (500 characters max).'; }

if ($tipo === 'producto') {
    $st = $pdo->prepare('SELECT nombre, stock FROM productos WHERE id = ?'); $st->execute([(int)($_POST['producto_id'] ?? 0)]); $p = $st->fetch();
    if (!$p) { $err[] = 'Product not found.'; }
    else {
        $cantidad = (int)($_POST['cantidad'] ?? 1);
        if ($cantidad < 1) { $err[] = 'Invalid quantity.'; }
        elseif ($cantidad > (int)$p['stock']) { $err[] = 'Quantity exceeds available stock (' . (int)$p['stock'] . ').'; }
        $ref = $p['nombre'];
    }
} elseif ($tipo === 'servicio') {
    $st = $pdo->prepare('SELECT pl.tipo_vehiculo, pl.precio, s.nombre FROM precios_lavado pl JOIN servicios s ON s.id = pl.servicio_id WHERE pl.id = ? AND s.activo = 1');
    $st->execute([(int)($_POST['precio_id'] ?? 0)]); $s = $st->fetch();
    if (!$s) { $err[] = 'Service not found.'; }
    else { $ref = VEHICULOS[$s['tipo_vehiculo']] . ' - ' . $s['nombre'] . ' (' . cop($s['precio']) . ')'; }
} elseif ($tipo === 'contacto') {
    $ref = trim($_POST['asunto'] ?? '');
    if ($ref === '' || mb_strlen($ref) > 100) { $err[] = 'Enter a subject (100 characters max).'; }
    if ($mensaje === '') { $err[] = 'Write a message.'; }
} else { $err[] = 'Invalid request type.'; }

if ($err) { flash('error', implode(' ', $err)); redirect($back); }

$pdo->prepare('INSERT INTO solicitudes (usuario_id, tipo, referencia, cantidad, nombre, telefono, email, mensaje) VALUES (?,?,?,?,?,?,?,?)')
    ->execute([$_SESSION['user']['id'] ?? null, $tipo, $ref, $cantidad, $nombre, $telefono, $email ?: null, $mensaje ?: null]);
flash('success', $tipo === 'contacto' ? 'Message sent! We will contact you soon.' : 'Request received! We will contact you shortly.');
redirect($back);
