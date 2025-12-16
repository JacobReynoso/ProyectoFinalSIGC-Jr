<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$proveedores = $db->query('SELECT id, nombre FROM proveedores ORDER BY nombre')->fetchAll();
$productos = $db->query('SELECT id, nombre FROM productos ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proveedor_id = (int)($_POST['proveedor_id'] ?? 0);
    $producto_id = (int)($_POST['producto_id'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $costo_unitario = (float)($_POST['costo_unitario'] ?? 0);
    $fecha = $_POST['fecha'] ?: date('Y-m-d');
    $notas = trim($_POST['notas'] ?? '');

    $db->beginTransaction();
    try {
        $ins = $db->prepare('INSERT INTO compras (proveedor_id, producto_id, cantidad, costo_unitario, fecha, notas) VALUES (?, ?, ?, ?, ?, ?)');
        $ins->execute([$proveedor_id, $producto_id, $cantidad, $costo_unitario, $fecha, $notas ?: null]);

        $stock = $db->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?');
        $stock->execute([$cantidad, $producto_id]);

        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo guardar: ' . $e->getMessage();
    }
}

echo renderHeader('Registrar compra');
?>
<div class="card">
  <h2>Nueva compra</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <div class="form-actions">
      <label for="proveedor_id">Proveedor</label>
      <select required name="proveedor_id" id="proveedor_id">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($proveedores as $pr): ?>
          <option value="<?= $pr['id'] ?>"><?= htmlspecialchars($pr['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-actions">
      <label for="producto_id">Producto</label>
      <select required name="producto_id" id="producto_id">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-actions">
      <label for="cantidad">Cantidad</label>
      <input type="number" name="cantidad" id="cantidad" value="1" min="1" />
    </div>
    <div class="form-actions">
      <label for="costo_unitario">Costo unitario</label>
      <input type="number" step="0.01" name="costo_unitario" id="costo_unitario" value="0" />
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
