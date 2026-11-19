<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
$yo = (int)$_SESSION['user']['id'];
if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/usuarios.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'eliminar') {
        if ($id === $yo) { flash('error', 'You cannot delete your own account.'); }
        else { $pdo->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$id]); flash('success', 'User deleted.'); }
    } elseif ($accion === 'guardar' && $id) {
        $nombre = trim($_POST['nombre'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $rol = $_POST['rol'] ?? ''; $pass = $_POST['password'] ?? ''; $err = [];
        if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) { $err[] = 'Name must be 3-100 characters.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $err[] = 'Invalid email address.'; }
        if (!in_array($rol, ['admin', 'cliente'], true)) { $err[] = 'Invalid role.'; }
        if ($id === $yo && $rol !== 'admin') { $err[] = 'You cannot remove your own administrator role.'; }
        if ($pass !== '' && (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass))) { $err[] = 'New password needs 8+ characters with letters and numbers.'; }
        $c = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ? AND id <> ?'); $c->execute([$email, $id]);
        if ($c->fetchColumn()) { $err[] = 'That email belongs to another user.'; }
        if ($err) { flash('error', implode(' ', $err)); redirect('admin/usuarios.php'); }
        if ($pass !== '') { $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, rol=?, password=? WHERE id=?')->execute([$nombre, $email, $rol, password_hash($pass, PASSWORD_DEFAULT), $id]); }
        else { $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?')->execute([$nombre, $email, $rol, $id]); }
        if ($id === $yo) { $_SESSION['user']['nombre'] = $nombre; $_SESSION['user']['email'] = $email; }
        flash('success', 'User updated.');
    }
    redirect('admin/usuarios.php');
}
$adminTitle = 'Manage Users';
$lista = $pdo->query('SELECT id, nombre, email, rol, creado_en FROM usuarios ORDER BY id')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Registered</th><th class="text-end">Actions</th></tr></thead><tbody>
  <?php foreach ($lista as $u): $json = ['id' => $u['id'], 'nombre' => $u['nombre'], 'email' => $u['email'], 'rol' => $u['rol']]; ?>
    <tr><td><?= e($u['nombre']) ?><?= (int)$u['id'] === $yo ? ' <span class="badge text-bg-info">You</span>' : '' ?></td><td><?= e($u['email']) ?></td>
      <td><span class="badge <?= $u['rol'] === 'admin' ? 'text-bg-primary' : 'text-bg-secondary' ?>"><?= $u['rol'] === 'admin' ? 'Administrator' : 'Customer' ?></span></td>
      <td class="text-secondary small"><?= e(date('Y-m-d', strtotime($u['creado_en']))) ?></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-info btn-editar" data-bs-toggle="modal" data-bs-target="#modalCrud" data-json="<?= e(json_encode($json)) ?>"><i class="bi bi-pencil"></i></button>
        <?php if ((int)$u['id'] !== $yo): ?>
        <form method="post" class="d-inline form-eliminar" data-msg="Delete user &quot;<?= e($u['nombre']) ?>&quot;?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        <?php endif; ?></td></tr>
  <?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="modalCrud" tabindex="-1"><div class="modal-dialog modal-dialog-centered">
  <form class="modal-content needs-validation" method="post" novalidate>
    <?= csrf_field() ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id">
    <div class="modal-header"><h5 class="modal-title" data-base="User">User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body vstack gap-3">
      <div><label class="form-label">Name</label><input name="nombre" class="form-control" required minlength="3" maxlength="100"><div class="invalid-feedback">Required (3-100 characters).</div></div>
      <div><label class="form-label">Email</label><input type="email" name="email" class="form-control" required><div class="invalid-feedback">Enter a valid email.</div></div>
      <div><label class="form-label">Role</label><select name="rol" class="form-select" required><option value="cliente">Customer</option><option value="admin">Administrator</option></select></div>
      <div><label class="form-label">New password (leave empty to keep the current one)</label><input type="password" name="password" class="form-control" minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"><div class="invalid-feedback">Min. 8 characters, letters and numbers.</div></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Save</button></div>
  </form></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
