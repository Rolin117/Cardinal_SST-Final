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
    FOREIGN KEY (id_cliente) REFERENCES tb_clientes(Hid_cliente)
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
