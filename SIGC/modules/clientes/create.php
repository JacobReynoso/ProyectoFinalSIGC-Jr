<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    $stmt = $db->prepare('INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nombre, $email ?: null, $telefono ?: null, $direccion ?: null]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Nuevo Cliente', 'Registra un nuevo cliente');
?>

<div class="card">
    <div class="card-header">
        <h2>Crear Cliente</h2>
    </div>
    <div class="card-content">
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input required name="nombre" id="nombre" type="text" placeholder="Razón social o nombre" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="correo@dominio.com" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input name="telefono" id="telefono" placeholder="555-0000" />
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input name="direccion" id="direccion" placeholder="Calle 123, Ciudad" />
                </div>
            </div>
            <div class="form-actions">
                <button class="btn-primary" type="submit">
                    <i class="fas fa-save"></i>
                    Guardar Cliente
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
