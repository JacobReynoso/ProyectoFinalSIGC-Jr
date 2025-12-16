<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$clientes = $db->query("SELECT * FROM clientes ORDER BY creado_en DESC")->fetchAll();

echo renderHeader('Clientes', 'Gestiona todos los clientes de tu negocio');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div></div>
    <a href="create.php" class="btn-nuevo">
        <i class="fas fa-plus"></i>
        Nuevo Cliente
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Lista de Clientes</h2>
            <p>Total: <?php echo count($clientes); ?> cliente(s)</p>
        </div>
    </div>
    <div class="card-content">
        <?php if(empty($clientes)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No hay clientes registrados</h3>
            <p>Comienza agregando tu primer cliente</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                <tr>
                    <td>#<?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c['telefono'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c['direccion'] ?? '-'); ?></td>
                    <td><?php echo substr($c['creado_en'], 0, 10); ?></td>
                    <td>
                        <div class="actions">
                            <a href="update.php?id=<?php echo $c['id']; ?>" class="btn-action" title="Editar">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $c['id']; ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Eliminar este cliente?')">
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
