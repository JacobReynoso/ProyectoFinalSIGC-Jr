# SIGC - Sistema Integral de Gestión Comercial

## Descripción

**SIGC** es una aplicación web integral para administrar procesos esenciales de un negocio: control de clientes, proveedores, productos, compras y ventas. Desarrollada con **PHP** y **MySQL**, ofrece operaciones CRUD completas con un diseño modular y una interfaz moderna accesible desde cualquier navegador web.

## Características principales

- ✅ **Gestión de Clientes**: Crear, leer, actualizar y eliminar registros de clientes
- ✅ **Gestión de Proveedores**: Administrar información de proveedores
- ✅ **Catálogo de Productos**: Control de productos con precios y stock
- ✅ **Registro de Compras**: Gestionar compras a proveedores con actualización automática de stock
- ✅ **Registro de Ventas**: Procesar ventas con control de disponibilidad
- ✅ **Panel de Control**: Estadísticas rápidas y resumen de operaciones
- ✅ **Transacciones seguras**: Manejo de integridad de datos en compras y ventas
- ✅ **Diseño responsivo**: Interfaz moderna y accesible

## Tecnologías aplicadas

| Tecnología | Versión | Descripción |
|-----------|---------|-------------|
| **PHP** | 7.4+ | Lenguaje de backend |
| **MySQL** | 5.7+ | Base de datos relacional |
| **HTML5** | — | Maquetación |
| **CSS3** | — | Estilos y diseño responsivo |
| **JavaScript** | ES6+ | Interactividad del lado cliente |
| **XAMPP** | Latest | Stack de desarrollo local |

## Requisitos previos

Asegúrate de tener instalados:

- **XAMPP** (versión 7.4 o superior) con Apache y MySQL activos
  - Descargar desde: https://www.apachefriends.org/
- **Cliente MySQL** (incluido en XAMPP) o MySQL Workbench para administración
- **Navegador web moderno** (Chrome, Firefox, Safari, Edge)
- Permisos de lectura/escritura en la carpeta htdocs de XAMPP

### Verificar instalación de XAMPP

```bash
# En macOS/Linux
sudo /opt/xampp/bin/xampp start

# En Windows (ejecutar como administrador)
C:\xampp\xampp_start.exe
```

## Instalación local

### 1. Clonar el repositorio

```bash
cd /opt/xampp/htdocs  # macOS/Linux
# O en Windows: C:\xampp\htdocs

git clone https://github.com/tuUsuario/sigc.git .
```

### 2. Importar la base de datos

#### Opción A: Usando phpMyAdmin

1. Abre http://localhost/phpmyadmin en tu navegador
2. Crea una nueva base de datos llamada `sigc`
3. Selecciona la base de datos y haz clic en "Importar"
4. Carga el archivo `/sql/ddl_sigc.sql`
5. Haz clic en "Ejecutar"

#### Opción B: Usando línea de comandos

```bash
# Acceder al cliente MySQL de XAMPP
cd /opt/xampp/bin  # macOS/Linux
./mysql -u root < /ruta/al/sql/ddl_sigc.sql

# En Windows:
cd C:\xampp\mysql\bin
mysql -u root < C:\xampp\htdocs\sql\ddl_sigc.sql
```

### 3. Validar estructura

Verifica que la carpeta tenga esta estructura:

```
/Applications/XAMPP/htdocs/
├── config/
│   └── db.php
├── modules/
│   ├── clientes/
│   │   ├── create.php
│   │   ├── read.php
│   │   ├── update.php
│   │   └── delete.php
│   ├── proveedores/
│   ├── productos/
│   ├── compras/
│   ├── ventas/
│   └── layout.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── sql/
│   ├── ddl_sigc.sql
│   └── erd.md
├── index.php
└── README.md
```

## Inicio rápido

### 1. Iniciar XAMPP

```bash
# macOS/Linux
sudo /opt/xampp/bin/xampp start

# Windows (Ejecutar como administrador)
C:\xampp\xampp_start.exe
```

Verifica que **Apache** y **MySQL** estén ✅ en verde.

### 2. Acceder a la aplicación

Abre tu navegador y ve a: **http://localhost**

### 3. Usar la aplicación

**Panel principal**: Estadísticas en tiempo real de clientes, productos y transacciones

**Menú de navegación** (en la barra superior):
- **Clientes**: Gestionar registro de clientes
- **Proveedores**: Administrar proveedores
- **Productos**: Control de catálogo
- **Compras**: Registrar compras y actualizar stock
- **Ventas**: Procesar ventas

## Funcionalidades por módulo

### Clientes (CRUD)
- **Create**: Nuevo cliente con email único
- **Read**: Lista completa con filtrado
- **Update**: Editar datos del cliente
- **Delete**: Eliminar con confirmación

### Proveedores (CRUD)
- Gestión completa de proveedores
- Relación con productos
- Filtro de búsqueda opcional

### Productos (CRUD)
- Crear productos con información de costo
- Asignar proveedor
- Control de stock en tiempo real
- Precios con decimales

### Compras (CRUD)
- Registrar compras de proveedores
- **Transacción atómica**: actualización automática de stock
- Historial con fechas
- Reversión de stock al eliminar

### Ventas (CRUD)
- Registrar ventas a clientes
- **Validación de stock**: evita sobreventa
- Precio de venta independiente del costo
- Reportes de ventas

## Estructura de la base de datos

### Tablas principales

**clientes**
```sql
id | nombre | email | telefono | direccion | creado_en
```

**proveedores**
```sql
id | nombre | contacto | telefono | email | direccion | creado_en
```

**productos**
```sql
id | nombre | descripcion | precio | stock | proveedor_id | creado_en
```

**compras**
```sql
id | proveedor_id | producto_id | cantidad | costo_unitario | fecha | notas
```

**ventas**
```sql
id | cliente_id | producto_id | cantidad | precio_unitario | fecha | notas
```

### Vistas disponibles

- `v_resumen_ventas`: Resumen de ventas con cálculo de totales
- `v_resumen_compras`: Resumen de compras con cálculo de totales

### Diagrama ER

Ver [sql/erd.md](sql/erd.md) para el diagrama de entidad-relación.

## Documentación de código

Cada módulo CRUD incluye:

### `/modules/clientes/create.php`
Formulario de creación con validación de campos obligatorios (nombre). Email debe ser único.

```php
// Ejemplo de inserción
$stmt = $pdo->prepare('INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)');
$stmt->execute([$nombre, $email, $telefono, $direccion]);
```

### `/modules/clientes/read.php`
Listado de todos los registros con botones de edición y eliminación.

```php
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();
```

### `/modules/clientes/update.php`
Edición de registros existentes con precarga de datos.

```php
$upd = $pdo->prepare('UPDATE clientes SET nombre=?, email=?, ... WHERE id=?');
$upd->execute([$nombre, $email, ..., $id]);
```

### `/modules/clientes/delete.php`
Eliminación con confirmación del usuario.

```php
$del = $pdo->prepare('DELETE FROM clientes WHERE id = ?');
$del->execute([$id]);
```

### `/modules/compras/` y `/modules/ventas/`
Incluyen **transacciones atómicas** para garantizar integridad:

```php
$pdo->beginTransaction();
try {
    // Insertar/actualizar documento
    // Actualizar stock
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
}
```

## Configuración de la base de datos

Archivo: `/config/db.php`

```php
function db(): PDO {
    $host = 'localhost';
    $db = 'sigc';
    $user = 'root';
    $pass = ''; // Cambiar si MySQL tiene contraseña
    
    return new PDO($dsn, $user, $pass, $options);
}
```

**Para cambiar credenciales MySQL:**
1. Abre `/config/db.php`
2. Modifica `$user`, `$pass` según tu configuración
3. Verifica que MySQL esté escuchando en `localhost:3306`

## Manejo de errores

La aplicación maneja:
- ✅ Registros no encontrados (HTTP 404)
- ✅ Errores de integridad referencial
- ✅ Stock insuficiente en ventas
- ✅ Emails duplicados
- ✅ Campos obligatorios vacíos

## Seguridad

- ✅ **Prepared statements** contra inyección SQL
- ✅ **htmlspecialchars()** contra XSS
- ✅ **Transacciones** para integridad de datos
- ✅ **Validación de entrada** en formularios

## Tips de uso

### Respaldar la base de datos

```bash
# Exportar DDL y datos
mysqldump -u root sigc > backup_sigc.sql

# Restaurar
mysql -u root sigc < backup_sigc.sql
```

### Ver registros en tiempo real

Usa phpMyAdmin: http://localhost/phpmyadmin

### Limpiar datos de ejemplo

```sql
DELETE FROM ventas;
DELETE FROM compras;
DELETE FROM productos;
DELETE FROM clientes;
DELETE FROM proveedores;
```

## Solución de problemas

| Problema | Solución |
|----------|----------|
| "PDOException: SQLSTATE[HY000]" | Verificar que MySQL esté corriendo en XAMPP |
| "Base de datos no encontrada" | Importar `sql/ddl_sigc.sql` en phpMyAdmin |
| Página en blanco | Revisar logs: `/opt/xampp/logs/` |
| 404 en navegación | Verificar estructura de carpetas |

## Entregables completados

- ✅ Repositorio GitHub con código organizado
- ✅ Scripts SQL (`sql/ddl_sigc.sql`) con datos de ejemplo
- ✅ Diagrama ER (`sql/erd.md`) en formato Mermaid
- ✅ CRUD funcional para 5 módulos
- ✅ Base de datos relacional con integridad
- ✅ Interface moderna y responsiva
- ✅ Documentación completa

## Próximos pasos (mejoras futuras)

- [ ] Autenticación de usuarios
- [ ] Reportes en PDF
- [ ] Gráficos de ventas/compras
- [ ] Búsqueda avanzada con filtros
- [ ] API REST
- [ ] Paginación de resultados
- [ ] Auditoría de cambios

## Licencia

Este proyecto es de código abierto bajo licencia MIT.

## Soporte

Para reportar bugs o propuestas de mejora, abre un issue en:
**GitHub Issues**

---

**Última actualización**: Diciembre 2024  
**Versión**: 1.0.0
