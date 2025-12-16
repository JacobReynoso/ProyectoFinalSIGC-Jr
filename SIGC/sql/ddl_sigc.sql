-- DDL para SIGC (Sistema Integral de Gestión Comercial)
-- Base de datos: sigc

CREATE DATABASE IF NOT EXISTS sigc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sigc;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE,
  telefono VARCHAR(60),
  direccion VARCHAR(200),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de proveedores
CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(140) NOT NULL,
  contacto VARCHAR(140),
  telefono VARCHAR(60),
  email VARCHAR(160),
  direccion VARCHAR(200),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(160) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  proveedor_id INT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_productos_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabla de compras
CREATE TABLE IF NOT EXISTS compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  proveedor_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  costo_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
  fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
  notas TEXT,
  CONSTRAINT fk_compras_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_compras_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
  fecha DATE NOT NULL DEFAULT (CURRENT_DATE),
  notas TEXT,
  CONSTRAINT fk_ventas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_ventas_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Vistas rápidas
CREATE OR REPLACE VIEW v_resumen_ventas AS
SELECT v.id, v.fecha, c.nombre AS cliente, p.nombre AS producto, v.cantidad, v.precio_unitario, (v.cantidad * v.precio_unitario) AS total
FROM ventas v
JOIN clientes c ON c.id = v.cliente_id
JOIN productos p ON p.id = v.producto_id;

CREATE OR REPLACE VIEW v_resumen_compras AS
SELECT c.id, c.fecha, pr.nombre AS proveedor, p.nombre AS producto, c.cantidad, c.costo_unitario, (c.cantidad * c.costo_unitario) AS total
FROM compras c
JOIN proveedores pr ON pr.id = c.proveedor_id
JOIN productos p ON p.id = c.producto_id;

-- Datos de ejemplo
INSERT INTO clientes (nombre, email, telefono, direccion) VALUES
('Acme Corp', 'ventas@acme.test', '555-1000', 'Calle Uno 123'),
('Distribuciones Nova', 'contacto@nova.test', '555-2000', 'Avenida Central 456');

INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES
('Proveedor Norte', 'Laura Méndez', '555-3000', 'lmendez@pnorte.test', 'Parque Industrial 12'),
('Importadora Sur', 'Diego Ruiz', '555-4000', 'druiz@isur.test', 'Zona Franca 9');

INSERT INTO productos (nombre, descripcion, precio, stock, proveedor_id) VALUES
('Laptop 14"', 'Equipo portátil para oficina', 850.00, 12, 1),
('Monitor 24"', 'Pantalla LED Full HD', 180.00, 25, 2),
('Mouse Inalámbrico', 'Mouse óptico con receptor USB', 18.50, 50, 1);

INSERT INTO compras (proveedor_id, producto_id, cantidad, costo_unitario, fecha, notas) VALUES
(1, 1, 5, 700.00, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), 'Reposición de laptops'),
(2, 2, 10, 140.00, DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY), 'Monitores para stock');

INSERT INTO ventas (cliente_id, producto_id, cantidad, precio_unitario, fecha, notas) VALUES
(1, 1, 2, 900.00, DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY), 'Venta corporativa'),
(2, 3, 5, 22.00, CURRENT_DATE, 'Pedido minorista');
