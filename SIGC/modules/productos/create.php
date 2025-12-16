<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/layout.php';

$db = db();
$proveedores = $db->query('SELECT id, nombre FROM proveedores ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $proveedor_id = $_POST['proveedor_id'] ? (int)$_POST['proveedor_id'] : null;

    $stmt = $db->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, proveedor_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$nombre, $descripcion ?: null, $precio, $stock, $proveedor_id]);
    header('Location: read.php');
    exit;
}

echo renderHeader('Crear producto');
?>
<div class="card">
  <h2>Nuevo producto</h2>
  <form method="post">
    <div class="form-actions">
      <label for="nombre">Nombre</label>
      <input required name="nombre" id="nombre" />
    </div>
    <div class="form-actions">
      <label for="descripcion">Descripción</label>
      <textarea class="form-control" name="descripcion" id="descripcion" rows="2"></textarea>
    </div>
    <div class="form-actions">
      <label for="precio">Precio</label>
      <input type="number" step="0.01" name="precio" id="precio" value="0" />
    </div>
    <div class="form-actions">
      <label for="stock">Stock</label>
      <input type="number" name="stock" id="stock" value="0" />
    </div>
    <div class="form-actions">
      <label for="proveedor_id">Proveedor</label>
      <select name="proveedor_id" id="proveedor_id">
        <option value="">-- Opcional --</option>
        <?php foreach ($proveedores as $pr): ?>
          <option value="<?= $pr['id'] ?>"><?= htmlspecialchars($pr['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-actions">
      <button class="btn-primary" type="submit">Guardar</button>
      <a class="btn-secondary" href="read.php">Cancelar</a>
    </div>
  </form>
</div>
<?php echo renderFooter(); ?>
