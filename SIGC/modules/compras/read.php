<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
// Usar vista para obtener datos con nombres descriptivos
$items = $db->query("
    SELECT c.id, c.fecha, pr.nombre AS proveedor, p.nombre AS producto, c.cantidad, c.costo_unitario, 
           (c.cantidad * c.costo_unitario) AS total
    FROM compras c
    JOIN proveedores pr ON pr.id = c.proveedor_id
    JOIN productos p ON p.id = c.producto_id
    ORDER BY c.fecha DESC
")->fetchAll();

echo renderHeader('Compras', 'Gestiona todas las compras');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div></div>
    <a href="create.php" class="btn-nuevo">
        <i class="fas fa-plus"></i>
        Nuevo Ucompra
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Lista de Ucompras</h2>
            <p>Total: <?php echo count($items); ?> Lcompras</p>
        </div>
    </div>
    <div class="card-content">
        <?php if(empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h3>No hay Lcompras registrados</h3>
            <p>Comienza agregando tu primer Lcompra</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>#<?php echo $item['id']; ?></td>
                    <td><?php echo substr($item['fecha'], 0, 10); ?></td>
                    <td><?php echo htmlspecialchars($item['proveedor']); ?></td>
                    <td><?php echo htmlspecialchars($item['producto']); ?></td>
                    <td><?php echo $item['cantidad']; ?></td>
                    <td>$<?php echo number_format($item['costo_unitario'], 2); ?></td>
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
