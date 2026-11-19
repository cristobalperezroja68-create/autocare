<?php
require_once __DIR__ . '/config/conexion.php';
if (!empty($_SESSION['user'])) { redirect($_SESSION['user']['rol'] === 'admin' ? 'admin/dashboard.php' : 'index.php'); }
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session. Please try again.'); redirect('login.php'); }
    $email = strtolower(trim($_POST['email'] ?? '')); $pass = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') { flash('error', 'Enter a valid email and password.'); redirect('login.php'); }
    $st = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?'); $st->execute([$email]); $u = $st->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'nombre' => $u['nombre'], 'email' => $u['email'], 'rol' => $u['rol']];
        flash('success', 'Welcome back, ' . $u['nombre'] . '!');
        redirect($u['rol'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
    }
    flash('error', 'Incorrect email or password.'); redirect('login.php');
}
$pageTitle = 'Login | AutoCare';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-6 col-lg-4">
  <form class="card p-4 needs-validation vstack gap-3" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="text-center"><img src="<?= BASE_URL ?>assets/img/logo.svg" width="56" alt=""><h3 class="mt-2">Login</h3></div>
    <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" required><div class="invalid-feedback">Enter a valid email.</div></div>
    <div><label class="form-label">Password</label><input type="password" name="password" class="form-control" required><div class="invalid-feedback">Enter your password.</div></div>
    <button class="btn btn-accent">Log in</button>
    <p class="text-center small mb-0">No account? <a href="<?= BASE_URL ?>registro.php">Register</a></p>
  </form>
</div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
