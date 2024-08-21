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
        $sql = 'SELECT id_pedido, fecha, total, id_cliente
                FROM tb_pedidos
                ORDER BY fecha DESC';
        return Database::getRows($sql);
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

    public function pedidosClientes()
    {
        $sql = 'SELECT 
                    c.nombre_cliente AS nombre_cliente,
                    c.apellido_cliente AS apellido_cliente,
                    p.fecha AS fecha_pedido,
                    p.total AS total_pedido
                FROM 
                    tb_pedidos p
                INNER JOIN 
                    tb_clientes c 
                ON 
                    p.id_cliente = c.id_cliente
                ORDER BY 
                    c.nombre_cliente, c.apellido_cliente, p.fecha DESC;
        ';

        return Database::getRows($sql);
    }
}
