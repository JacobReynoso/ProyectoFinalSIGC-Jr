<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$clientes = $db->query('SELECT id, nombre FROM clientes ORDER BY nombre')->fetchAll();
$productos = $db->query('SELECT id, nombre, stock, precio FROM productos ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $producto_id = (int)($_POST['producto_id'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $precio_unitario = (float)($_POST['precio_unitario'] ?? 0);
    $fecha = $_POST['fecha'] ?: date('Y-m-d');
    $notas = trim($_POST['notas'] ?? '');

    $db->beginTransaction();
    try {
        // Bloquea stock actual del producto
        $lock = $db->prepare('SELECT stock FROM productos WHERE id = ? FOR UPDATE');
        $lock->execute([$producto_id]);
        $stockRow = $lock->fetch();
        if (!$stockRow) {
            throw new RuntimeException('Producto no encontrado');
        }
        if ($stockRow['stock'] < $cantidad) {
            throw new RuntimeException('Stock insuficiente');
        }

        $ins = $db->prepare('INSERT INTO ventas (cliente_id, producto_id, cantidad, precio_unitario, fecha, notas) VALUES (?, ?, ?, ?, ?, ?)');
        $ins->execute([$cliente_id, $producto_id, $cantidad, $precio_unitario, $fecha, $notas ?: null]);

        $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?')->execute([$cantidad, $producto_id]);

        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo guardar: ' . $e->getMessage();
    }
}

echo renderHeader('Registrar venta');
?>
<div class="card">
  <h2>Nueva venta</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="form-actions">
      <label for="cliente_id">Cliente</label>
      <select required name="cliente_id" id="cliente_id">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($clientes as $cl): ?>
          <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-actions">
      <label for="producto_id">Producto</label>
      <select required name="producto_id" id="producto_id">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>" data-price="<?= $p['precio'] ?>"><?= htmlspecialchars($p['nombre']) ?> (stock: <?= (int)$p['stock'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-actions">
      <label for="cantidad">Cantidad</label>
      <input type="number" name="cantidad" id="cantidad" value="1" min="1" />
    </div>
    <div class="form-actions">
      <label for="precio_unitario">Precio unitario</label>
      <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" value="0" />
    </div>
    <div class="form-actions">
      <label for="fecha">Fecha</label>
      <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" />
    </div>
    <div class="form-actions">
      <label for="notas">Notas</label>
      <textarea class="form-control" name="notas" id="notas" rows="2"></textarea>
    </div>
    <div class="form-actions">
      <button class="btn-primary" type="submit">Guardar</button>
      <a class="btn-secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
