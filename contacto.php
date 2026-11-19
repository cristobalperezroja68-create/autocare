<?php $pageTitle = 'Contact | AutoCare'; require_once __DIR__ . '/includes/header.php'; $u = $_SESSION['user'] ?? null; ?>
<div class="page-head"><div class="container"><h1>Contact</h1><p class="text-secondary mb-0">Questions, quotes or feedback? Write to us.</p></div></div>
<div class="container py-4"><div class="row g-4">
  <div class="col-lg-7"><form class="card p-4 needs-validation vstack gap-3" method="post" action="<?= BASE_URL ?>solicitar.php" novalidate>
    <?= csrf_field() ?><input type="hidden" name="tipo" value="contacto">
    <div><label class="form-label">Name</label><input class="form-control" name="nombre" required minlength="3" maxlength="100" value="<?= e($u['nombre'] ?? '') ?>"><div class="invalid-feedback">Enter your name.</div></div>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="telefono" required pattern="[0-9+\s()\-]{7,20}"><div class="invalid-feedback">Enter a valid phone number.</div></div>
      <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e($u['email'] ?? '') ?>"></div>
    </div>
    <div><label class="form-label">Subject</label><input class="form-control" name="asunto" required maxlength="100"><div class="invalid-feedback">Enter a subject.</div></div>
    <div><label class="form-label">Message</label><textarea class="form-control" name="mensaje" rows="5" required maxlength="500"></textarea><div class="invalid-feedback">Write a message.</div></div>
    <button class="btn btn-accent">Send message</button>
  </form></div>
  <div class="col-lg-5"><div class="card p-4 h-100"><h5>Visit us</h5>
    <p class="text-secondary"><i class="bi bi-geo-alt text-accent"></i> <?= e(NEGOCIO_DIR) ?></p>
    <p class="text-secondary"><i class="bi bi-telephone text-accent"></i> <?= e(NEGOCIO_TEL) ?></p>
    <p class="text-secondary"><i class="bi bi-envelope text-accent"></i> <?= e(NEGOCIO_EMAIL) ?></p>
    <p class="text-secondary mb-0"><i class="bi bi-clock text-accent"></i> <?= e(NEGOCIO_HORARIO) ?></p>
  </div></div>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
