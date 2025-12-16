<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$items = $db->query("SELECT * FROM proveedores ORDER BY creado_en DESC")->fetchAll();

echo renderHeader('Uproveedores','Gestiona todos los proveedores');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div></div>
    <a href="create.php" class="btn-nuevo">
        <i class="fas fa-plus"></i>
        Nuevo Uproveedore
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Lista de Uproveedores</h2>
            <p>Total: <?php echo count($items); ?> Lproveedores</p>
        </div>
    </div>
    <div class="card-content">
        <?php if(empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <h3>No hay Lproveedores registrados</h3>
            <p>Comienza agregando tu primer Lproveedore</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>#<?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($item['contacto'] ?? $item['nombre'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($item['telefono'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($item['email'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($item['direccion'] ?? '-'); ?></td>
                    <td>
                        <div class="actions">
                            <a href="update.php?id=<?php echo $item['id']; ?>" class="btn-action" title="Editar">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Eliminar?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php echo renderFooter(); ?>
