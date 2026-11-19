<?php
// config/conexion.php - database connection, session and helper functions
if (session_status() === PHP_SESSION_NONE) { session_start(); }

define('BASE_URL', '/autocare/');          // change it if you rename the project folder
define('ROOT_PATH', dirname(__DIR__));
define('NEGOCIO_TEL', '+57 300 123 4567');
define('NEGOCIO_EMAIL', 'contact@autocare.com');
define('NEGOCIO_DIR', 'Calle 10 # 20-30, Medellín, Colombia');
define('NEGOCIO_HORARIO', 'Mon - Sat: 8:00 am - 6:00 pm');
const VEHICULOS = ['moto' => 'Motorcycle', 'carro' => 'Car', 'camioneta' => 'Pickup Truck'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=autocare_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $ex) {
    die('Database connection error. Make sure MySQL is running in XAMPP and autocare_db was imported.');
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function cop($n) { return '$' . number_format((float)$n, 0, ',', '.') . ' COP'; }
function is_post() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function redirect($path) { header('Location: ' . BASE_URL . $path); exit; }
function flash($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
function csrf() { if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); } return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_ok() { return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']); }
function flash_script() {
    if (empty($_SESSION['flash'])) { return; }
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    $icon = in_array($f['type'], ['success', 'error', 'warning', 'info'], true) ? $f['type'] : 'info';
    $fl = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    echo '<script>Swal.fire({icon:' . json_encode($icon, $fl) . ',title:' . json_encode($f['msg'], $fl) .
         ',confirmButtonColor:"#7c4dff",background:"#1b1b22",color:"#fff"});</script>';
}
