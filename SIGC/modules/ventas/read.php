<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
// Usar vista para obtener datos con nombres descriptivos
$items = $db->query("
    SELECT v.id, v.fecha, c.nombre AS cliente, p.nombre AS producto, v.cantidad, v.precio_unitario,
           (v.cantidad * v.precio_unitario) AS total
    FROM ventas v
    JOIN clientes c ON c.id = v.cliente_id
    JOIN productos p ON p.id = v.producto_id
    ORDER BY v.fecha DESC
")->fetchAll();

echo renderHeader('Ventas', 'Gestiona todas las ventas');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div></div>
    <a href="create.php" class="btn-nuevo">
        <i class="fas fa-plus"></i>
        Nueva Venta
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Lista de Ventas</h2>
            <p>Total: <?php echo count($items); ?> ventas</p>
        </div>
    </div>
    <div class="card-content">
        <?php if(empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <h3>No hay ventas registradas</h3>
            <p>Comienza agregando tu primera venta</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>#<?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($item['cliente']); ?></td>
                    <td><?php echo htmlspecialchars($item['producto']); ?></td>
                    <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                    <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                    <td><strong>$<?php echo number_format($item['total'], 2); ?></strong></td>
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
