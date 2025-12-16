<?php
/**
 * SIGC - Sistema Integral de Gestión Comercial
 * Dashboard Principal - Nuevo Diseño
 */

$basePath = dirname(__FILE__);
require_once $basePath . '/config/db.php';

$db = db();

// Obtener estadísticas
$stats = [];

// Total de clientes
$result = $db->query("SELECT COUNT(*) as total FROM clientes");
$stats['clientes'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Total de proveedores
$result = $db->query("SELECT COUNT(*) as total FROM proveedores");
$stats['proveedores'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Total de productos
$result = $db->query("SELECT COUNT(*) as total FROM productos");
$stats['productos'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Total de compras
$result = $db->query("SELECT COUNT(*) as total FROM compras");
$stats['compras'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Total de ventas
$result = $db->query("SELECT COUNT(*) as total FROM ventas");
$stats['ventas'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Ingresos totales (ventas): precio_unitario * cantidad
$result = $db->query("SELECT COALESCE(SUM(cantidad * precio_unitario), 0) AS total FROM ventas");
$stats['ingresos'] = floatval($result->fetch(PDO::FETCH_ASSOC)['total']);

// Egresos totales (compras): costo_unitario * cantidad
$result = $db->query("SELECT COALESCE(SUM(cantidad * costo_unitario), 0) AS total FROM compras");
$stats['egresos'] = floatval($result->fetch(PDO::FETCH_ASSOC)['total']);

// Ventas del mes
$result = $db->query("
    SELECT COALESCE(SUM(cantidad * precio_unitario), 0) AS total 
    FROM ventas 
    WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())
");
$stats['ventas_mes'] = floatval($result->fetch(PDO::FETCH_ASSOC)['total']);

// Clientes recientes
$clientes_recientes = $db->query("
    SELECT id, nombre, email, telefono 
    FROM clientes 
    ORDER BY creado_en DESC 
    LIMIT 5
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIGC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fb;
            color: #333;
        }

        .container-main {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar-header {
            padding: 24px 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
            color: #1a73e8;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: #1a73e8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #666;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            margin: 0 8px 0 0;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #e3f2fd;
            color: #1a73e8;
            border-left-color: #1a73e8;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid #e9ecef;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            margin-left: 220px;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f5f7fb;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 8px 12px;
            width: 300px;
        }

        .search-box input {
            border: none;
            background: none;
            outline: none;
            flex: 1;
            font-size: 14px;
        }

        .btn-nuevo {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-nuevo:hover {
            background: #1557b0;
        }

        /* CONTENT AREA */
        .content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #222;
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-info h3 {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #222;
        }

        .stat-change {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        .stat-change.positive {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stat-change.negative {
            background: #ffebee;
            color: #c62828;
        }

        /* TABLE */
        .card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 16px;
            font-weight: 600;
            color: #222;
        }

        .card-header p {
            font-size: 13px;
            color: #999;
            margin-top: 4px;
        }

        .card-link {
            color: #1a73e8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .card-content {
            padding: 20px 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        td {
            padding: 12px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 14px;
            padding: 4px;
            transition: color 0.2s;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-action:hover {
            color: #1a73e8;
        }

        .btn-action.delete:hover {
            color: #d32f2f;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -220px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s;
            }

            .sidebar.open {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .search-box {
                width: auto;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/SIGC/" class="logo">
                    <div class="logo-icon">S</div>
                    <span>SIGC</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <a href="/SIGC/" class="nav-item active">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/SIGC/modules/clientes/read.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
                <a href="/SIGC/modules/proveedores/read.php" class="nav-item">
                    <i class="fas fa-building"></i>
                    <span>Proveedores</span>
                </a>
                <a href="/SIGC/modules/productos/read.php" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
                <a href="/SIGC/modules/compras/read.php" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Compras</span>
                </a>
                <a href="/SIGC/modules/ventas/read.php" class="nav-item">
                    <i class="fas fa-receipt"></i>
                    <span>Ventas</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- HEADER -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar...">
                    </div>
                </div>
                <button class="btn-nuevo" onclick="alert('Nuevo registro')">
                    <i class="fas fa-plus"></i>
                    Nuevo Registro
                </button>
            </header>

            <!-- CONTENT -->
            <div class="content">
                <!-- STATS -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Clientes</h3>
                            <div class="stat-number"><?php echo number_format($stats['clientes']); ?></div>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +12%
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Productos</h3>
                            <div class="stat-number"><?php echo number_format($stats['productos']); ?></div>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +8%
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Ventas del Mes</h3>
                            <div class="stat-number">$<?php echo number_format($stats['ventas_mes'], 0); ?></div>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> +23%
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Compras Pendientes</h3>
                            <div class="stat-number"><?php echo number_format($stats['compras']); ?></div>
                        </div>
                        <div class="stat-change negative">
                            <i class="fas fa-arrow-down"></i> -5%
                        </div>
                    </div>
                </div>

                <!-- CLIENTES RECIENTES -->
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2>Clientes Recientes</h2>
                            <p>Listado de los últimos clientes registrados</p>
                        </div>
                        <a href="/SIGC/modules/clientes/read.php" class="card-link">Ver Todos</a>
                    </div>
                    <div class="card-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($clientes_recientes as $cliente): ?>
                                <tr>
                                    <td>#<?php echo $cliente['id']; ?></td>
                                    <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['telefono'] ?? '-'); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="/SIGC/modules/clientes/read.php?id=<?php echo $cliente['id']; ?>" class="btn-action" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/SIGC/modules/clientes/update.php?id=<?php echo $cliente['id']; ?>" class="btn-action" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="/SIGC/modules/clientes/delete.php?id=<?php echo $cliente['id']; ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Estás seguro?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html>
