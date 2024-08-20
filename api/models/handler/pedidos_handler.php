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
    

}
