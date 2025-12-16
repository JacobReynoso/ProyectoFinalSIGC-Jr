<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM clientes WHERE id = ?');
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    http_response_code(404);
    exit('Cliente no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $db->prepare('DELETE FROM clientes WHERE id = ?');
    $del->execute([$id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Eliminar Cliente', 'Confirma la eliminación del cliente');
?>

<div class="card">
    <div class="card-header">
        <h2>Eliminar Cliente</h2>
    </div>
    <div class="card-content">
        <div style="background: #ffebee; border-left: 4px solid #c62828; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
            <p style="color: #c62828; font-weight: 600; margin-bottom: 8px;">
                <i class="fas fa-exclamation-triangle"></i> Advertencia
            </p>
            <p>¿Estás seguro de que deseas eliminar a <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>?</p>
            <p style="margin-top: 8px; font-size: 12px; color: #999;">Esta acción no se puede deshacer.</p>
        </div>

        <form method="post" style="display: flex; gap: 12px;">
            <button class="btn-danger" type="submit">
                <i class="fas fa-trash"></i>
                Sí, Eliminar
            </button>
            <a class="btn-secondary" href="read.php">
                <i class="fas fa-times"></i>
                Cancelar
            </a>
        </form>
    </div>
</div>

<?php echo renderFooter(); ?>
}

render_header('Eliminar cliente');
?>
<div class="card">
  <h2>Eliminar cliente</h2>
  <p>Confirma que deseas eliminar a <strong><?= htmlspecialchars($cliente['nombre']) ?></strong>.</p>
  <form method="post" onsubmit="return confirmDelete('Esta acción no se puede deshacer, ¿continuar?');">
    <button class="btn danger" type="submit">Eliminar</button>
    <a class="btn secondary" href="read.php">Cancelar</a>
  </form>
</div>
<?php render_footer(); ?>
