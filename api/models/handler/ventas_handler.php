<?php

require_once('../../helpers/database.php');
require 'vendor/autoload.php';

use Phpml\Regression\LeastSquares;
use Phpml\Math\Matrix;

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
    fecha_venta
LIMIT 12;
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

    public function predictVentasMensuales()
{
    // Consulta para obtener las ventas mensuales
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
                mes ASC;"; // Ordenar ascendentemente para predicciones cronológicas
    $rows = Database::getRows($sql);

    if (empty($rows)) {
        return [];
    }

    // Preparar datos para la regresión
    $meses = [];
    $ventas = [];

    foreach ($rows as $row) {
        $date = DateTime::createFromFormat('Y-m', $row['mes']);
        $meses[] = $date->getTimestamp(); // Convertir fecha a timestamp
        $ventas[] = $row['total_ventas'];
    }

    $predicciones = [];
    $numMesesFuturos = 3; // Número de meses para predecir

    // Calcular la regresión para los próximos meses
    for ($i = 1; $i <= $numMesesFuturos; $i++) {
        $X = array_slice($meses, 0, count($meses));
        $y = array_slice($ventas, 0, count($ventas));

        // Crear el modelo de regresión lineal
        $regression = new LeastSquares();
        $regression->train(array_map(function ($timestamp) {
            return [$timestamp];
        }, $X), $y);

        // Predecir las ventas para el mes
        $timestamp = end($meses) + $i * 30 * 24 * 60 * 60; // Sumar aproximadamente 1 mes en segundos
        $predictedSales = $regression->predict([$timestamp]);

        // Redondear el resultado a 2 decimales
        $predictedSales = round($predictedSales, 2);
        // Convertir timestamp a mes en formato 'Y-m'
        $date = (new DateTime())->setTimestamp($timestamp)->format('Y-m');

        $predicciones[] = [
            'mes' => $date,
            'total_ventas' => $predictedSales
        ];
    }

    return $predicciones;
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
