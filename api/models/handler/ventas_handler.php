<?php

require_once('../../helpers/database.php');
require('/var/www/html/Cardinal_SST-Final/vendor/autoload.php');

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

    public function predictVentasMensuales($limit = 10)
    {
        // Consulta para obtener las ventas mensuales con un límite
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
                mes ASC
            LIMIT $limit;"; // Limitar el número de registros
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



    public function InventarioProductosP($limit = 10)
    {
        $sql = "SELECT 
    p.nombre_producto,
    DATE_FORMAT(v.fecha_venta, '%Y-%m') AS mes,
    SUM(v.cantidad_vendida) AS total_vendido
FROM 
    tb_ventas v
INNER JOIN 
    tb_productos p ON v.id_producto = p.id_producto
GROUP BY 
    p.nombre_producto, mes
ORDER BY 
    p.nombre_producto, mes ASC;
";
        $rows = Database::getRows($sql);

        if (empty($rows)) {
            return [];
        }

        $productos = [];
        $predicciones = [];
        $numMesesFuturos = 3;

        foreach ($rows as $row) {
            $producto = $row['nombre_producto'];
            $date = DateTime::createFromFormat('Y-m', $row['mes']);
            $mes = $date->getTimestamp();
            $vendido = $row['total_vendido'];

            if (!isset($productos[$producto])) {
                $productos[$producto] = ['meses' => [], 'vendido' => []];
            }
            $productos[$producto]['meses'][] = $mes;
            $productos[$producto]['vendido'][] = $vendido;
        }

        foreach ($productos as $producto => $data) {
            $meses = $data['meses'];
            $vendido = $data['vendido'];

            if (count($meses) < 2) {
                // No se puede realizar regresión con menos de dos puntos
                continue;
            }

            for ($i = 1; $i <= $numMesesFuturos; $i++) {
                $X = array_map(function ($timestamp) {
                    return [$timestamp];
                }, $meses);
                $y = $vendido;

                try {
                    $regression = new LeastSquares();
                    $regression->train($X, $y);

                    $timestamp = end($meses) + $i * 30 * 24 * 60 * 60;
                    $predictedSales = $regression->predict([$timestamp]);

                    $predictedSales = round($predictedSales, 2);
                    $date = (new DateTime())->setTimestamp($timestamp)->format('Y-m');

                    $predicciones[] = [
                        'producto' => $producto,
                        'mes' => $date,
                        'total_vendido' => $predictedSales
                    ];
                } catch (\Phpml\Exception\MatrixException $e) {
                    // Manejar el caso de matriz singular (puedes loggear o manejar de otra manera)
                    continue;
                }
            }
        }

        return $predicciones;
    }
}
