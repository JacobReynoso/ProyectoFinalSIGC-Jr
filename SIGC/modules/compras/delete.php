<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM compras WHERE id = ?');
$stmt->execute([$id]);
$compra = $stmt->fetch();

if (!$compra) {
    http_response_code(404);
    exit('Compra no encontrada');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->beginTransaction();
    try {
        $db->prepare('DELETE FROM compras WHERE id = ?')->execute([$id]);
        $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?')->execute([$compra['cantidad'], $compra['producto_id']]);
        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo eliminar: ' . $e->getMessage();
    }
}

echo renderHeader('Eliminar compra');
?>
<div class="card">
  <h2>Eliminar compra</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <p>Se eliminará la compra <strong>#<?= $compra['id'] ?></strong> y se ajustará el stock.</p>
  <form method="post" onsubmit="return confirmDelete('¿Eliminar la compra y revertir stock?');">
    <button class="btn danger" type="submit">Eliminar</button>
    <a class="btn secondary" href="read.php">Cancelar</a>
  </form>
</div>
<?php echo renderFooter(); ?>
