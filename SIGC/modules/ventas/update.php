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
        // Devolver stock de la venta previa
        $db->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?')->execute([$venta['cantidad'], $venta['producto_id']]);

        // Bloquear y validar stock del nuevo producto
        $lock = $db->prepare('SELECT stock FROM productos WHERE id = ? FOR UPDATE');
        $lock->execute([$producto_id]);
        $stockRow = $lock->fetch();
        if (!$stockRow) {
            throw new RuntimeException('Producto no encontrado');
        }
        if ($stockRow['stock'] < $cantidad) {
            throw new RuntimeException('Stock insuficiente');
        }

        $upd = $db->prepare('UPDATE ventas SET cliente_id=?, producto_id=?, cantidad=?, precio_unitario=?, fecha=?, notas=? WHERE id=?');
        $upd->execute([$cliente_id, $producto_id, $cantidad, $precio_unitario, $fecha, $notas ?: null, $id]);

        $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?')->execute([$cantidad, $producto_id]);

        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo actualizar: ' . $e->getMessage();
    }
}

echo renderHeader('Editar venta');
?>
<div class="card">
  <h2>Editar venta</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <div>
      <label for="cliente_id">Cliente</label>
      <select required name="cliente_id" id="cliente_id">
        <?php foreach ($clientes as $cl): ?>
          <option value="<?= $cl['id'] ?>" <?= $venta['cliente_id'] == $cl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cl['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="producto_id">Producto</label>
      <select required name="producto_id" id="producto_id">
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $venta['producto_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?> (stock: <?= (int)$p['stock'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="cantidad">Cantidad</label>
      <input type="number" name="cantidad" id="cantidad" value="<?= (int)$venta['cantidad'] ?>" min="1" />
    </div>
    <div>
      <label for="precio_unitario">Precio unitario</label>
      <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" value="<?= htmlspecialchars($venta['precio_unitario']) ?>" />
    </div>
    <div>
      <label for="fecha">Fecha</label>
      <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($venta['fecha']) ?>" />
    </div>
    <div>
      <label for="notas">Notas</label>
      <textarea name="notas" id="notas" rows="2"><?= htmlspecialchars($venta['notas']) ?></textarea>
    </div>
    <div>
      <button class="btn" type="submit">Actualizar</button>
      <a class="btn secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
