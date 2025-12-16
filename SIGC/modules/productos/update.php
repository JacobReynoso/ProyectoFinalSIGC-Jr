<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$productoStmt = $db->prepare('SELECT * FROM productos WHERE id = ?');
$productoStmt->execute([$id]);
$producto = $productoStmt->fetch();

if (!$producto) {
    http_response_code(404);
    exit('Producto no encontrado');
}

$proveedores = $db->query('SELECT id, nombre FROM proveedores ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $proveedor_id = $_POST['proveedor_id'] ? (int)$_POST['proveedor_id'] : null;

    $upd = $db->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, proveedor_id=? WHERE id=?');
    $upd->execute([$nombre, $descripcion ?: null, $precio, $stock, $proveedor_id, $id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Editar producto');
?>
<div class="card">
  <h2>Editar producto</h2>
  <form method="post">
    <div>
      <label for="nombre">Nombre</label>
      <input required name="nombre" id="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" />
    </div>
    <div>
      <label for="descripcion">Descripción</label>
      <textarea name="descripcion" id="descripcion" rows="2"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
    </div>
    <div>
      <label for="precio">Precio</label>
      <input type="number" step="0.01" name="precio" id="precio" value="<?= htmlspecialchars($producto['precio']) ?>" />
    </div>
    <div>
      <label for="stock">Stock</label>
      <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($producto['stock']) ?>" />
    </div>
    <div>
      <label for="proveedor_id">Proveedor</label>
      <select name="proveedor_id" id="proveedor_id">
        <option value="">-- Opcional --</option>
        <?php foreach ($proveedores as $pr): ?>
          <option value="<?= $pr['id'] ?>" <?= $producto['proveedor_id'] == $pr['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <button class="btn" type="submit">Actualizar</button>
      <a class="btn secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
