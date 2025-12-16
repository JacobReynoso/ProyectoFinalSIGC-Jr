<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    http_response_code(404);
    exit('Producto no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $db->prepare('DELETE FROM productos WHERE id = ?');
    $del->execute([$id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Eliminar producto');
?>
<div class="card">
  <h2>Eliminar producto</h2>
  <p>Confirma que deseas eliminar <strong><?= htmlspecialchars($producto['nombre']) ?></strong>.</p>
  <form method="post" onsubmit="return confirmDelete('Esta acción no se puede deshacer, ¿continuar?');">
    <button class="btn danger" type="submit">Eliminar</button>
    <a class="btn secondary" href="read.php">Cancelar</a>
  </form>
</div>
<?php echo renderFooter(); ?>
