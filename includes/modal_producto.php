<?php $mu = $_SESSION['user'] ?? null; ?>
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content needs-validation" method="post" action="<?= BASE_URL ?>solicitar.php" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="tipo" value="producto">
      <input type="hidden" name="producto_id" id="prodId">
      <div class="modal-header"><h5 class="modal-title">Inquire / Buy: <span id="prodNombre"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body vstack gap-3">
        <div><label class="form-label">Your name</label><input class="form-control" name="nombre" required minlength="3" maxlength="100" value="<?= e($mu['nombre'] ?? '') ?>"><div class="invalid-feedback">Enter your name.</div></div>
        <div><label class="form-label">Phone</label><input class="form-control" name="telefono" required pattern="[0-9+\s()\-]{7,20}"><div class="invalid-feedback">Enter a valid phone number.</div></div>
        <div><label class="form-label">Email (optional)</label><input type="email" class="form-control" name="email" value="<?= e($mu['email'] ?? '') ?>"></div>
        <div><label class="form-label">Quantity</label><input type="number" class="form-control" name="cantidad" id="prodCantidad" value="1" min="1" required></div>
        <div><label class="form-label">Message (optional)</label><textarea class="form-control" name="mensaje" rows="2" maxlength="500"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-accent">Send request</button></div>
    </form>
  </div>
</div>
