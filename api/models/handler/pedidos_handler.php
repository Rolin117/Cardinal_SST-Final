<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla pedidos.
 */
class PedidoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $fecha = null;
    protected $total = null;
    protected $id_cliente = null;

    /*
     *  Métodos para realizar las operaciones CRUD (create, read, update, and delete).
     */
    public function createRow()
    {
        $sql = 'INSERT INTO tb_pedidos(fecha, total, id_cliente)
                VALUES(?, ?, ?)';
        $params = array($this->fecha, $this->total, $this->id_cliente);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT p.id_pedido, p.fecha, p.total, c.nombre_cliente
                FROM tb_pedidos p
                INNER JOIN tb_clientes c ON p.id_cliente = c.id_cliente
                ORDER BY p.fecha DESC';
        return Database::getRows($sql);
    }


    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT p.id_pedido, p.fecha, p.total, c.nombre_cliente, c.apellido_cliente, c.correo_cliente, c.telefono_cliente
            FROM tb_pedidos p
            INNER JOIN tb_clientes c ON p.id_cliente = c.id_cliente
            WHERE c.nombre_cliente LIKE ? OR c.apellido_cliente LIKE ? OR c.correo_cliente LIKE ? OR c.telefono_cliente LIKE ?
            ORDER BY p.fecha DESC';
        $params = array($value, $value, $value, $value);
        return Database::getRows($sql, $params);
    }


    public function getClientePorPedido()
    {
        $sql = 'SELECT c.id_cliente, c.nombre_cliente, c.apellido_cliente, c.correo_cliente, c.telefono_cliente
                FROM tb_pedidos p
                INNER JOIN tb_clientes c ON p.id_cliente = c.id_cliente
                WHERE p.id_pedido = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function readOne()
    {
        $sql = 'SELECT id_pedido, fecha, total, id_cliente
                FROM tb_pedidos
                WHERE id_pedido = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_pedidos
                SET fecha = ?, total = ?, id_cliente = ?
                WHERE id_pedido = ?';
        $params = array($this->fecha, $this->total, $this->id_cliente, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_pedidos
                WHERE id_pedido = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /* Funciones de reportes */
    public function IngresosMes()
    {
        $sql = "SELECT 
            DATE_FORMAT(fecha, '%Y-%m') AS mes,
            SUM(total) AS ingresos_totales
        FROM 
            tb_pedidos
        GROUP BY 
            mes
        ORDER BY 
            mes DESC";

        return Database::getRows($sql);
    }

    public function prediccionIngresosMes()
    {
        $sql = "SELECT 
                DATE_FORMAT(v.fecha_venta, '%Y-%m') AS mes,
                    SUM(p.precio_producto * v.cantidad_vendida * (1 - IFNULL(o.descuento, 0) / 100)) AS ingresos_totales
                FROM 
                    tb_ventas v
                INNER JOIN 
                    tb_productos p ON v.id_producto = p.id_producto
                LEFT JOIN 
                    tb_ofertas o ON v.id_oferta = o.id_oferta
                GROUP BY 
                    mes
                ORDER BY 
                    mes DESC;
                ";

        return Database::getRows($sql);
    }


    public function pedidosMes()
    {
        $sql = "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') AS mes,
                    COUNT(id_pedido) AS total_pedidos,
                    SUM(total) AS ingresos_totales
                FROM 
                    tb_pedidos
                GROUP BY
                    mes
                ORDER BY 
                    mes DESC;
                ";
        return Database::getRows($sql);
    }

    public function pedidosPorCliente()
    {
        $sql = 'SELECT p.id_pedido, p.fecha, p.total, dp.cantidad, pr.nombre_producto
                FROM tb_pedidos p
                INNER JOIN tb_detalle_pedido dp ON p.id_pedido = dp.id_pedido
                INNER JOIN tb_productos pr ON dp.id_producto = pr.id_producto
                WHERE p.id_cliente = ?';
        $params = array($this->id_cliente);
        return Database::getRows($sql, $params);
    }


    /*Graficos*/
    public function pedidosPorClienteG()
    {
        $sql = "SELECT 
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente, 
    COUNT(p.id_pedido) AS total_pedidos
FROM 
    tb_clientes c
INNER JOIN 
    tb_pedidos p ON c.id_cliente = p.id_cliente
GROUP BY 
    cliente;
";
        return Database::getRows($sql);
    }
}
