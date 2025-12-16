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
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    $upd = $db->prepare('UPDATE clientes SET nombre=?, email=?, telefono=?, direccion=? WHERE id=?');
    $upd->execute([$nombre, $email ?: null, $telefono ?: null, $direccion ?: null, $id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Editar Cliente', 'Actualiza la información del cliente');
?>

<div class="card">
    <div class="card-header">
        <h2>Editar Cliente #<?php echo $id; ?></h2>
    </div>
    <div class="card-content">
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input required name="nombre" id="nombre" type="text" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input name="telefono" id="telefono" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input name="direccion" id="direccion" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>" />
                </div>
            </div>
            <div class="form-actions">
                <button class="btn-primary" type="submit">
                    <i class="fas fa-save"></i>
                    Actualizar Cliente
                </button>
                <a class="btn-secondary" href="read.php">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php echo renderFooter(); ?>
