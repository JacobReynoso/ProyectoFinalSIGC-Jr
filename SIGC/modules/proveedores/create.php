<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    $stmt = $db->prepare('INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$nombre, $contacto ?: null, $telefono ?: null, $email ?: null, $direccion ?: null]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Crear proveedor');
?>
<div class="card">
  <h2>Nuevo proveedor</h2>
  <form method="post">
    <div class="form-actions">
      <label for="nombre">Nombre</label>
      <input required name="nombre" id="nombre" />
    </div>
    <div class="form-actions">
      <label for="contacto">Contacto</label>
      <input name="contacto" id="contacto" />
    </div>
    <div class="form-actions">
      <label for="telefono">Teléfono</label>
      <input name="telefono" id="telefono" />
    </div>
    <div class="form-actions">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" />
    </div>
    <div class="form-actions">
      <label for="direccion">Dirección</label>
      <textarea class="form-control" name="direccion" id="direccion" rows="2"></textarea>
    </div>
    <div class="form-actions">
      <button class="btn-primary" type="submit">Guardar</button>
      <a class="btn-secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
