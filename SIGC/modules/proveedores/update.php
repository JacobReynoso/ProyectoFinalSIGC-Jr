<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM proveedores WHERE id = ?');
$stmt->execute([$id]);
$proveedor = $stmt->fetch();

if (!$proveedor) {
    http_response_code(404);
    exit('Proveedor no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    $upd = $db->prepare('UPDATE proveedores SET nombre=?, contacto=?, telefono=?, email=?, direccion=? WHERE id=?');
    $upd->execute([$nombre, $contacto ?: null, $telefono ?: null, $email ?: null, $direccion ?: null, $id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Editar proveedor');
?>
<div class="card">
  <h2>Editar proveedor</h2>
  <form method="post">
    <div>
      <label for="nombre">Nombre</label>
      <input required name="nombre" id="nombre" value="<?= htmlspecialchars($proveedor['nombre']) ?>" />
    </div>
    <div>
      <label for="contacto">Contacto</label>
      <input name="contacto" id="contacto" value="<?= htmlspecialchars($proveedor['contacto']) ?>" />
    </div>
    <div>
      <label for="telefono">Teléfono</label>
      <input name="telefono" id="telefono" value="<?= htmlspecialchars($proveedor['telefono']) ?>" />
    </div>
    <div>
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($proveedor['email']) ?>" />
    </div>
    <div>
      <label for="direccion">Dirección</label>
      <textarea name="direccion" id="direccion" rows="2"><?= htmlspecialchars($proveedor['direccion']) ?></textarea>
    </div>
    <div>
      <button class="btn" type="submit">Actualizar</button>
      <a class="btn secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
