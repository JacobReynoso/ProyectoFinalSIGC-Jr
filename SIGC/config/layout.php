<?php
/**
 * Layout base para todas las páginas de SIGC
 * Incluir antes del contenido y cerrar con el footer
 */

function renderHeader($pageTitle = '', $pageSubtitle = '') {
    $basePath = '/SIGC';
    $currentPage = basename($_SERVER['PHP_SELF']);
    $currentModule = isset($_GET['module']) ? $_GET['module'] : (isset($GLOBALS['current_module']) ? $GLOBALS['current_module'] : '');
    
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($pageTitle ? $pageTitle . ' - ' : '') . 'SIGC'; ?></title>
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
            text-decoration: none;
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

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #999;
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

        /* CARD */
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

        .card-content {
            padding: 20px 24px;
        }

        /* TABLE */
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

        /* FORMS */
        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #222;
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #1a73e8;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        /* BUTTONS */
        .btn-primary {
            background: #1a73e8;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover {
            background: #1557b0;
        }

        .btn-secondary {
            background: #f5f7fb;
            color: #1a73e8;
            padding: 10px 16px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .btn-danger {
            background: #d32f2f;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        /* ALERTS */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left-color: #2e7d32;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left-color: #c62828;
        }

        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-left-color: #e65100;
        }

        .alert-info {
            background: #e3f2fd;
            color: #0d47a1;
            border-left-color: #0d47a1;
        }

        /* RESPONSIVE */
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

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #666;
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
                <a href="/SIGC/" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/SIGC/') === 0 && strpos($_SERVER['REQUEST_URI'], 'modules') === false) ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/SIGC/modules/clientes/read.php" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], 'clientes') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
                <a href="/SIGC/modules/proveedores/read.php" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], 'proveedores') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Proveedores</span>
                </a>
                <a href="/SIGC/modules/productos/read.php" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], 'productos') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
                <a href="/SIGC/modules/compras/read.php" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], 'compras') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Compras</span>
                </a>
                <a href="/SIGC/modules/ventas/read.php" class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], 'ventas') !== false ? 'active' : ''; ?>">
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
            </header>

            <!-- CONTENT -->
            <div class="content">
                <?php if($pageTitle): ?>
                <div class="page-header">
                    <h1 class="page-title"><?php echo $pageTitle; ?></h1>
                    <?php if($pageSubtitle): ?>
                    <p class="page-subtitle"><?php echo $pageSubtitle; ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
    <?php
    return ob_get_clean();
}

function renderFooter() {
    return '
            </div>
        </div>
    </div>

    <script>
        document.getElementById("menuToggle").addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("open");
        });
    </script>
</body>
</html>
    ';
}
?>
