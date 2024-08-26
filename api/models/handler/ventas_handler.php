<?php

require_once('../../helpers/database.php');

class ventaHandler
{
    // Declaración de atributos para el manejo de datos.
    protected $id = null;
    protected $id_producto = null;
    protected $cantidad_vendida = null;
    protected $fecha_venta = null;
    protected $id_oferta = null;

    /*
     *  Métodos para realizar las operaciones CRUD (create, read, update, and delete).
     */

    // Método para buscar registros en la tabla de ventas.
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT v.id_venta, p.nombre_producto, v.cantidad_vendida, v.fecha_venta, o.nombre_oferta
                FROM tb_ventas v
                INNER JOIN tb_productos p ON v.id_producto = p.id_producto
                LEFT JOIN tb_ofertas o ON v.id_oferta = o.id_oferta
                WHERE p.nombre_producto LIKE ? OR o.nombre_oferta LIKE ?
                ORDER BY v.fecha_venta DESC';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    // Método para crear un nuevo registro en la tabla de ventas.
    public function createRow()
    {
        $sql = 'INSERT INTO tb_ventas(id_producto, cantidad_vendida, fecha_venta, id_oferta)
                VALUES(?, ?, ?, ?)';
        $params = array($this->id_producto, $this->cantidad_vendida, $this->fecha_venta, $this->id_oferta);
        return Database::executeRow($sql, $params);
    }

    // Método para leer todos los registros de la tabla de ventas.
    public function readAll()
    {
        $sql = 'SELECT v.id_venta, p.nombre_producto, v.cantidad_vendida, v.fecha_venta, o.nombre_oferta
                FROM tb_ventas v
                INNER JOIN tb_productos p ON v.id_producto = p.id_producto
                LEFT JOIN tb_ofertas o ON v.id_oferta = o.id_oferta
                ORDER BY v.fecha_venta DESC';
        return Database::getRows($sql);
    }

    // Método para leer un registro específico de la tabla de ventas.
    public function readOne()
    {
        $sql = 'SELECT v.id_venta, p.nombre_producto, v.cantidad_vendida, v.fecha_venta, o.nombre_oferta
                FROM tb_ventas v
                INNER JOIN tb_productos p ON v.id_producto = p.id_producto
                LEFT JOIN tb_ofertas o ON v.id_oferta = o.id_oferta
                WHERE v.id_venta = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para actualizar un registro en la tabla de ventas.
    public function updateRow()
    {
        $sql = 'UPDATE tb_ventas
                SET id_producto = ?, cantidad_vendida = ?, fecha_venta = ?, id_oferta = ?
                WHERE id_venta = ?';
        $params = array($this->id_producto, $this->cantidad_vendida, $this->fecha_venta, $this->id_oferta, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un registro en la tabla de ventas.
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_ventas
                WHERE id_venta = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /*Graficos*/

    public function getVentasTotalesPorFecha()
    {
        $sql = 'SELECT 
    fecha_venta, 
    SUM(cantidad_vendida * p.precio_producto) AS total_ventas
FROM 
    tb_ventas v
INNER JOIN 
    tb_productos p ON v.id_producto = p.id_producto
GROUP BY 
    fecha_venta;

        ';
        return Database::getRows($sql);
    }

    public function ventasPorProducto()
    {
        $sql = 'SELECT 
                    p.nombre_producto, 
                    SUM(v.cantidad_vendida) AS total_vendida
                FROM 
                    tb_ventas v
                INNER JOIN 
                    tb_productos p ON v.id_producto = p.id_producto
                    GROUP BY 
                    p.nombre_producto;
                ';

        return Database::getRows($sql);
    }

    public function VentasMensualesP()
    {
        $sql = "SELECT 
                DATE_FORMAT(fecha_venta, '%Y-%m') AS mes,
                    SUM(cantidad_vendida * p.precio_producto) AS total_ventas
                FROM 
                    tb_ventas v
                INNER JOIN 
                    tb_productos p ON v.id_producto = p.id_producto
                GROUP BY 
                    mes
                ORDER BY 
                    mes DESC
                LIMIT 12; -- Ajusta este límite según el rango de datos que necesites
                ";
        return Database::getRows($sql);
    }

    public function InventarioProductosP()
    {
        $sql = "SELECT 
                    p.nombre_producto,
                    SUM(v.cantidad_vendida) AS total_vendido
                FROM 
                    tb_ventas v
                INNER JOIN 
                    tb_productos p ON v.id_producto = p.id_producto
                GROUP BY 
                    p.nombre_producto
                ORDER BY 
                    total_vendido DESC
                LIMIT 10; -- Ajusta este límite según el número de productos
                ";
        return Database::getRows($sql);
    }
}
