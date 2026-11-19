<?php
require_once __DIR__ . '/config/conexion.php';
if (!empty($_SESSION['user'])) { redirect('index.php'); }
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session. Please try again.'); redirect('registro.php'); }
    $nombre = trim($_POST['nombre'] ?? ''); $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? ''; $pass2 = $_POST['password2'] ?? ''; $err = [];
    if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) { $err[] = 'Name must be 3-100 characters.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 120) { $err[] = 'Invalid email address.'; }
    if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) { $err[] = 'Password must be at least 8 characters with letters and numbers.'; }
    if ($pass !== $pass2) { $err[] = 'Passwords do not match.'; }
    if (!$err) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?'); $st->execute([$email]);
        if ($st->fetchColumn()) { $err[] = 'That email is already registered.'; }
    }
    if ($err) { flash('error', implode(' ', $err)); redirect('registro.php'); }
    $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,"cliente")')
        ->execute([$nombre, $email, password_hash($pass, PASSWORD_DEFAULT)]);
    flash('success', 'Account created! You can log in now.'); redirect('login.php');
}
$pageTitle = 'Register | AutoCare';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-6 col-lg-5">
  <form class="card p-4 needs-validation vstack gap-3" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="text-center"><img src="<?= BASE_URL ?>assets/img/logo.svg" width="56" alt=""><h3 class="mt-2">Create account</h3></div>
    <div><label class="form-label">Full name</label><input name="nombre" class="form-control" required minlength="3" maxlength="100"><div class="invalid-feedback">Enter your name (min. 3 characters).</div></div>
    <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" required><div class="invalid-feedback">Enter a valid email.</div></div>
    <div><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"><div class="invalid-feedback">Min. 8 characters, letters and numbers.</div></div>
    <div><label class="form-label">Confirm password</label><input type="password" name="password2" class="form-control" required><div class="invalid-feedback">Passwords must match.</div></div>
    <button class="btn btn-accent">Register</button>
    <p class="text-center small mb-0">Already registered? <a href="<?= BASE_URL ?>login.php">Log in</a></p>
  </form>
</div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
