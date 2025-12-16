<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM compras WHERE id = ?');
$stmt->execute([$id]);
$compra = $stmt->fetch();

if (!$compra) {
    http_response_code(404);
    exit('Compra no encontrada');
}

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
        // Revertir stock anterior
        $db->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?')->execute([$compra['cantidad'], $compra['producto_id']]);

        // Actualizar compra
        $upd = $db->prepare('UPDATE compras SET proveedor_id=?, producto_id=?, cantidad=?, costo_unitario=?, fecha=?, notas=? WHERE id=?');
        $upd->execute([$proveedor_id, $producto_id, $cantidad, $costo_unitario, $fecha, $notas ?: null, $id]);

        // Aplicar nuevo stock
        $db->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?')->execute([$cantidad, $producto_id]);

        $db->commit();
        header('Location: read.php');
        exit;
    } catch (Throwable $e) {
        $db->rollBack();
        $error = 'No se pudo actualizar: ' . $e->getMessage();
    }
}

echo renderHeader('Editar compra');
?>
<div class="card">
  <h2>Editar compra</h2>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <div>
      <label for="proveedor_id">Proveedor</label>
      <select required name="proveedor_id" id="proveedor_id">
        <?php foreach ($proveedores as $pr): ?>
          <option value="<?= $pr['id'] ?>" <?= $compra['proveedor_id'] == $pr['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="producto_id">Producto</label>
      <select required name="producto_id" id="producto_id">
        <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $compra['producto_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="cantidad">Cantidad</label>
      <input type="number" name="cantidad" id="cantidad" value="<?= (int)$compra['cantidad'] ?>" min="1" />
    </div>
    <div>
      <label for="costo_unitario">Costo unitario</label>
      <input type="number" step="0.01" name="costo_unitario" id="costo_unitario" value="<?= htmlspecialchars($compra['costo_unitario']) ?>" />
    </div>
    <div>
      <label for="fecha">Fecha</label>
      <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($compra['fecha']) ?>" />
    </div>
    <div>
      <label for="notas">Notas</label>
      <textarea name="notas" id="notas" rows="2"><?= htmlspecialchars($compra['notas']) ?></textarea>
    </div>
    <div>
      <button class="btn" type="submit">Actualizar</button>
      <a class="btn secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
