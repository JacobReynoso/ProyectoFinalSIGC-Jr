<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM ventas WHERE id = ?');
$stmt->execute([$id]);
$venta = $stmt->fetch();

if (!$venta) {
    http_response_code(404);
    exit('Venta no encontrada');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->beginTransaction();
    try {
        $db->prepare('DELETE FROM ventas WHERE id = ?')->execute([$id]);
        $db->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?')->execute([$venta['cantidad'], $venta['producto_id']]);
        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo eliminar: ' . $e->getMessage();
    }
}

echo renderHeader('Eliminar venta');
?>
<div class="card">
  <h2>Eliminar venta</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <p>Se eliminará la venta <strong>#<?= $venta['id'] ?></strong> y se repondrá el stock.</p>
  <form method="post" onsubmit="return confirmDelete('¿Eliminar la venta y reponer stock?');">
    <button class="btn danger" type="submit">Eliminar</button>
    <a class="btn secondary" href="read.php">Cancelar</a>
  </form>
</div>
<?php echo renderFooter(); ?>
