<?php
function render_header(string $title = 'SIGC') : void {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> | SIGC</title>
        <link rel="stylesheet" href="/assets/css/style.css">
        <script src="/assets/js/app.js" defer></script>
    </head>
    <body>
    <header>
        <h1>SIGC · Gestión Comercial</h1>
        <nav>
            <a href="/index.php">Inicio</a>
            <a href="/modules/clientes/read.php">Clientes</a>
            <a href="/modules/proveedores/read.php">Proveedores</a>
            <a href="/modules/productos/read.php">Productos</a>
            <a href="/modules/compras/read.php">Compras</a>
            <a href="/modules/ventas/read.php">Ventas</a>
        </nav>
    </header>
    <div class="main">
    <?php
}

function render_footer(): void {
    ?>
      <div class="footer">SIGC · PHP + MySQL · XAMPP</div>
    </div>
    </body>
    </html>
    <?php
}
