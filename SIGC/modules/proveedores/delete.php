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
    $del = $db->prepare('DELETE FROM proveedores WHERE id = ?');
    $del->execute([$id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Eliminar proveedor');
?>
<div class="card">
  <h2>Eliminar proveedor</h2>
  <p>Confirma que deseas eliminar a <strong><?= htmlspecialchars($proveedor['nombre']) ?></strong>.</p>
  <form method="post" onsubmit="return confirmDelete('Esta acción no se puede deshacer, ¿continuar?');">
    <button class="btn danger" type="submit">Eliminar</button>
    <a class="btn secondary" href="read.php">Cancelar</a>
  </form>
</div>
<?php echo renderFooter(); ?>
