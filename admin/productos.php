<?php
require_once __DIR__ . '/../includes/verificar_sesion.php'; require_admin();
$dir = ROOT_PATH . '/assets/img/productos/';

function subir_imagen($file, &$error) {
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) { return null; }
    if ($file['error'] !== UPLOAD_ERR_OK) { $error = 'Image upload failed.'; return false; }
    if ($file['size'] > 2 * 1024 * 1024) { $error = 'Image must be 2 MB or smaller.'; return false; }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext || !@getimagesize($file['tmp_name'])) { $error = 'Only JPG, PNG or WEBP images are allowed.'; return false; }
    global $dir;
    if (!is_dir($dir)) { mkdir($dir, 0755, true); }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) { $error = 'Could not save the image.'; return false; }
    return $name;
}

if (is_post()) {
    if (!csrf_ok()) { flash('error', 'Invalid session token.'); redirect('admin/productos.php'); }
    $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'eliminar') {
        $st = $pdo->prepare('SELECT imagen FROM productos WHERE id = ?'); $st->execute([$id]); $img = $st->fetchColumn();
        $pdo->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
        if ($img && is_file($dir . $img)) { unlink($dir . $img); }
        flash('success', 'Product deleted.'); redirect('admin/productos.php');
    }
    if ($accion === 'guardar') {
        $nombre = trim($_POST['nombre'] ?? ''); $desc = trim($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? ''; $stock = $_POST['stock'] ?? ''; $cat = (int)($_POST['categoria_id'] ?? 0); $err = [];
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 120) { $err[] = 'Name must be 2-120 characters.'; }
        if (mb_strlen($desc) > 1000) { $err[] = 'Description is too long.'; }
        if (!is_numeric($precio) || $precio < 0 || $precio > 99999999) { $err[] = 'Invalid price.'; }
        if (filter_var($stock, FILTER_VALIDATE_INT) === false || $stock < 0) { $err[] = 'Invalid stock quantity.'; }
        $c = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id = ?'); $c->execute([$cat]);
        if (!$c->fetchColumn()) { $err[] = 'Select a valid category.'; }
        $nueva = null;
        if (!$err) { $msg = ''; $nueva = subir_imagen($_FILES['imagen'] ?? null, $msg); if ($nueva === false) { $err[] = $msg; } }
        if ($err) { flash('error', implode(' ', $err)); redirect('admin/productos.php'); }
        if ($id) {
            $st = $pdo->prepare('SELECT imagen FROM productos WHERE id = ?'); $st->execute([$id]); $old = $st->fetchColumn();
            if ($nueva) {
                $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=?, imagen=? WHERE id=?')->execute([$nombre, $desc, $precio, $stock, $cat, $nueva, $id]);
                if ($old && is_file($dir . $old)) { unlink($dir . $old); }
            } else {
                $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=? WHERE id=?')->execute([$nombre, $desc, $precio, $stock, $cat, $id]);
            }
            flash('success', 'Product updated.');
        } else {
            $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen) VALUES (?,?,?,?,?,?)')->execute([$nombre, $desc, $precio, $stock, $cat, $nueva]);
            flash('success', 'Product added.');
        }
        redirect('admin/productos.php');
    }
}
$adminTitle = 'Manage Products';
$cats = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
$lista = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC')->fetchAll();
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="d-flex justify-content-between mb-3"><span class="text-secondary"><?= count($lista) ?> products</span>
  <button class="btn btn-accent btn-nuevo" data-bs-toggle="modal" data-bs-target="#modalCrud"><i class="bi bi-plus-lg"></i> Add product</button></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th class="text-end">Actions</th></tr></thead><tbody>
  <?php foreach ($lista as $p): $img = $p['imagen'] ? BASE_URL . 'assets/img/productos/' . $p['imagen'] : BASE_URL . 'assets/img/placeholder.svg';
    $json = ['id' => $p['id'], 'nombre' => $p['nombre'], 'descripcion' => $p['descripcion'], 'precio' => $p['precio'], 'stock' => $p['stock'], 'categoria_id' => $p['categoria_id']]; ?>
    <tr><td><img src="<?= e($img) ?>" class="thumb" alt=""></td><td><?= e($p['nombre']) ?></td><td><?= e($p['categoria']) ?></td><td><?= cop($p['precio']) ?></td>
      <td><span class="badge <?= $p['stock'] > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= (int)$p['stock'] ?></span></td>
      <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-outline-info btn-editar" data-bs-toggle="modal" data-bs-target="#modalCrud" data-json="<?= e(json_encode($json)) ?>"><i class="bi bi-pencil"></i></button>
        <form method="post" class="d-inline form-eliminar" data-msg="Delete &quot;<?= e($p['nombre']) ?>&quot;?"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
  <?php endforeach; ?></tbody></table></div></div>

<div class="modal fade" id="modalCrud" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg">
  <form class="modal-content needs-validation" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id">
    <div class="modal-header"><h5 class="modal-title" data-base="Product">Product</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body row g-3">
      <div class="col-md-8"><label class="form-label">Name</label><input name="nombre" class="form-control" required minlength="2" maxlength="120"><div class="invalid-feedback">Required (2-120 characters).</div></div>
      <div class="col-md-4"><label class="form-label">Category</label><select name="categoria_id" class="form-select" required><option value="">Select...</option><?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['nombre']) ?></option><?php endforeach; ?></select><div class="invalid-feedback">Select a category.</div></div>
      <div class="col-12"><label class="form-label">Description</label><textarea name="descripcion" class="form-control" rows="3" maxlength="1000"></textarea></div>
      <div class="col-md-4"><label class="form-label">Price (COP)</label><input type="number" name="precio" class="form-control" min="0" step="100" required><div class="invalid-feedback">Enter a valid price.</div></div>
      <div class="col-md-4"><label class="form-label">Stock</label><input type="number" name="stock" class="form-control" min="0" step="1" required><div class="invalid-feedback">Enter the stock quantity.</div></div>
      <div class="col-md-4"><label class="form-label">Image (JPG/PNG/WEBP, max 2 MB)</label><input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Save</button></div>
  </form></div></div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
