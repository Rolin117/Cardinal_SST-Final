DROP DATABASE IF EXISTS `db_cardinal`;
CREATE DATABASE db_cardinal;

USE db_cardinal;

CREATE TABLE tb_administradores (
    id_administrador INT PRIMARY KEY AUTO_INCREMENT,
    nombre_admin VARCHAR(200) NOT NULL,
    apellido_admin VARCHAR(200) NOT NULL,
    correo_admin VARCHAR(250) NOT NULL UNIQUE,
    telefono_admin varchar(9) NOT NULL,
    contrasenia_admin VARCHAR(100) NOT NULL
);

CREATE TABLE tb_clientes (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nombre_cliente VARCHAR(200) NOT NULL,
    apellido_cliente VARCHAR(200) NOT NULL,
    correo_cliente VARCHAR(250) UNIQUE NOT NULL,
    telefono_cliente varchar(9) NOT NULL,
    contrasenia_cliente VARCHAR(100) UNIQUE
);

CREATE TABLE tb_categorias (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_cat VARCHAR(100) UNIQUE NOT NULL,
    descripcion_cat VARCHAR(200) NOT NULL,
    imagen VARCHAR(25)
);

CREATE TABLE tb_productos (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(200) UNIQUE NOT NULL,
    precio_producto DECIMAL(10,2) NOT NULL,
    descripcion VARCHAR(250) NOT NULL,
    cantidad_producto INT NOT NULL,
    imagen_producto VARCHAR(25),
    id_categoria INT,
    id_admin INT,
    FOREIGN KEY (id_categoria) REFERENCES tb_categorias(id_categoria),
    FOREIGN KEY (id_admin) REFERENCES tb_administradores(id_administrador)
);

CREATE TABLE tb_ofertas (
    id_oferta INT PRIMARY KEY AUTO_INCREMENT,
    nombre_oferta VARCHAR(200) UNIQUE NOT NULL,
    descripcion_oferta VARCHAR(200) NOT NULL,
    descuento INT(11) NOT NULL,
    id_producto INT,
    FOREIGN KEY (id_producto) REFERENCES tb_productos(id_producto)
);

CREATE TABLE tb_servicios (
    id_servicio INT PRIMARY KEY AUTO_INCREMENT,
    nombre_servicio VARCHAR(200) UNIQUE NOT NULL,
    descripcion_servicio VARCHAR(250) NOT NULL,
    id_admin INT,
    FOREIGN KEY (id_admin) REFERENCES tb_administradores(id_administrador)
);

CREATE TABLE tb_pedidos (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    id_cliente INT,
    FOREIGN KEY (id_cliente) REFERENCES tb_clientes(id_cliente)
);

CREATE TABLE tb_detalle_pedido (
    id_detalle_pedido INT PRIMARY KEY AUTO_INCREMENT,
    precio DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,
    id_pedido INT,
    id_producto INT,
    FOREIGN KEY (id_pedido) REFERENCES tb_pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES tb_productos(id_producto)
);

CREATE TABLE tb_ventas (
	id_venta INT PRIMARY KEY AUTO_INCREMENT,
    id_producto INT,
    cantidad_vendida INT,
	fecha_venta DATE,
    id_oferta INT,
    FOREIGN KEY (id_producto) REFERENCES tb_productos(id_producto),
    FOREIGN KEY (id_oferta) REFERENCES tb_ofertas(id_oferta)
);


ALTER TABLE tb_ofertas
ADD COLUMN estado ENUM('activa', 'inactiva') DEFAULT 'activa';


ALTER TABLE tb_ofertas
ADD COLUMN fecha_inicio DATE DEFAULT NULL,
ADD COLUMN fecha_fin DATE DEFAULT NULL;

DELIMITER $$

CREATE TRIGGER tr_registrar_venta
AFTER INSERT ON tb_detalle_pedido
FOR EACH ROW
BEGIN
    DECLARE id_oferta INT;

    -- Verificar si el producto del detalle de pedido está en oferta
    SELECT o.id_oferta 
    INTO id_oferta
    FROM tb_ofertas o
    WHERE o.id_producto = NEW.id_producto 
    AND CURDATE() BETWEEN o.fecha_inicio AND o.fecha_fin 
    LIMIT 1;

    -- Si el producto está en oferta, registrar la venta en tb_ventas
    IF id_oferta IS NOT NULL THEN
        INSERT INTO tb_ventas (id_producto, cantidad_vendida, fecha_venta, id_oferta)
        VALUES (NEW.id_producto, NEW.cantidad, CURDATE(), id_oferta);
    END IF;
END$$

DELIMITER ;

SELECT 
    a.nombre_admin AS nombre_administrador,
    a.apellido_admin AS apellido_administrador,
    p.nombre_producto,
    p.precio_producto
FROM 
    tb_productos p
INNER JOIN 
    tb_administradores a 
ON 
    p.id_admin = a.id_administrador
ORDER BY 
    a.nombre_admin, a.apellido_admin;



INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, correo_cliente, telefono_cliente, contrasenia_cliente)
VALUES 
('Jorge', 'López', 'jorge.lopez@correo.com', '912345678', 'cliente123'),
('Lucía', 'Méndez', 'lucia.mendez@correo.com', '987654321', 'cliente456'),
('Miguel', 'Ramírez', 'miguel.ramirez@correo.com', '923456789', 'cliente789'),
('Sara', 'Vargas', 'sara.vargas@correo.com', '921376548', 'cliente101'),
('Alberto', 'Castro', 'alberto.castro@correo.com', '987321654', 'cliente202');

INSERT INTO tb_pedidos (fecha, total, id_cliente)
VALUES 
('2024-08-15', 319.98, 1),
('2024-08-16', 499.99, 2),
('2024-08-17', 419.98, 3),
('2024-08-18', 19.99, 4),
('2024-08-19', 199.99, 5);

-- Insertar datos en la tabla tb_categorias
INSERT INTO tb_categorias (nombre_cat, descripcion_cat)
VALUES 
('Cat4', 'Descripcion1'),
('Cat5', 'Descripcion2'),
('Cat6', 'Descripcion3'),
('Cat7', 'Descripcion4'),
('Cat8', 'Descripcion5');

-- Insertar datos en la tabla tb_administradores
INSERT INTO tb_administradores (nombre_admin, apellido_admin, correo_admin, telefono_admin, contrasenia_admin)
VALUES 
('Carlos', 'Pérez', 'carlos.perez@correo.com', '987654321', 'admin123'),
('María', 'Gómez', 'maria.gomez@correo.com', '912345678', 'admin456'),
('Luis', 'Fernández', 'luis.fernandez@correo.com', '987321654', 'admin789'),
('Ana', 'Martínez', 'ana.martinez@correo.com', '921376548', 'admin101'),
('Pedro', 'Sánchez', 'pedro.sanchez@correo.com', '923456789', 'admin202');

INSERT INTO tb_servicios (nombre_servicio, descripcion_servicio, id_admin)
VALUES 
('Servicio1', 'Descripcion1', 1),
('Servicio2', 'Descripcion2', 2),
('Servicio3', 'Descripcion3', 3),
('Servicio4', 'Descripcion4', 4),
('Servicio5', 'Descripcion5', 5);

INSERT INTO tb_productos (nombre_producto, precio_producto, descripcion, cantidad_producto, id_categoria, id_admin)
VALUES 
('Producto1', 299.99, 'Descripcion1', 50, 1, 1),
('Producto2', 499.99, 'Descripcion2', 30, 1, 2),
('Producto3', 399.99, 'Descripcion3', 20, 2, 3),
('Producto4', 19.99, 'Descripcion4', 100,  3, 4),
('Producto5', 199.99, 'Descripcion5', 15, 5, 6);

-- Insertar datos en la tabla tb_ofertas
INSERT INTO tb_ofertas (nombre_oferta, descripcion_oferta, descuento, id_producto, estado, fecha_inicio, fecha_fin)
VALUES 
('Oferta1', 'Descripcion1', 10, 1, 'activa', '2024-08-01', '2024-08-31'),
('Oferta2', 'Descripcion2', 15, 2, 'activa', '2024-08-10', '2024-09-10'),
('Oferta3', 'Descripcion3', 20, 3, 'inactiva', NULL, NULL),
('Oferta4', 'Descripcion4', 5, 4, 'activa', '2024-08-15', '2024-08-25'),
('Oferta5', 'Descripcion5', 25, 5, 'inactiva', NULL, NULL);

INSERT INTO tb_detalle_pedido (precio, total, cantidad, id_pedido, id_producto)
VALUES 
(299.99, 299.99, 1, 1, 1),
(19.99, 19.99, 1, 1, 4),
(499.99, 499.99, 1, 2, 2),
(399.99, 399.99, 1, 3, 3),
(19.99, 19.99, 1, 4, 4);

 INSERT INTO tb_ventas (id_producto, cantidad_vendida, fecha_venta, id_oferta)
VALUES 
(1, 8, '2024-05-07', NULL),
(2, 12, '2024-05-10', NULL),
(3, 7, '2024-06-18', NULL),
(4, 25, '2024-07-20', NULL),
(5, 15, '2024-08-22', NULL);

INSERT INTO tb_productos (nombre_producto, precio_producto, descripcion, cantidad_producto, id_categoria, id_admin)
VALUES 
('Producto6', 149.99, 'Descripcion1', 3, 1, 3),
('Producto7', 49.99, 'Descripcion2', 2, 1, 4);

INSERT INTO tb_productos (nombre_producto, precio_producto, descripcion, cantidad_producto, id_categoria, id_admin)
VALUES 
('Producto8', 899.99, 'Descripcion6', 0, 1, 1),
('Producto9', 199.99, 'Descripcion7', 0, 1, 2);


SELECT * FROM tb_administradores;

SELECT c.id_cliente, c.nombre_cliente, c.apellido_cliente, c.correo_cliente, c.telefono_cliente
                FROM tb_pedidos p
                INNER JOIN tb_clientes c ON p.id_cliente = c.id_cliente
                WHERE p.id_pedido = 2