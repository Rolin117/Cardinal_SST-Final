<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla detalle_pedido.
 */
class DetallePedidoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $precio = null;
    protected $total = null;
    protected $cantidad = null;
    protected $id_pedido = null;
    protected $id_producto = null;

    /*
     *  Métodos para realizar las operaciones CRUD (create, read, update, and delete).
     */
    public function createRow()
    {
        $sql = 'INSERT INTO tb_detalle_pedido(precio, total, cantidad, id_pedido, id_producto)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->precio, $this->total, $this->cantidad, $this->id_pedido, $this->id_producto);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_detalle_pedido, precio, total, cantidad, id_pedido, id_producto
                FROM tb_detalle_pedido
                ORDER BY id_detalle_pedido';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_detalle_pedido, precio, total, cantidad, id_pedido, id_producto
                FROM tb_detalle_pedido
                WHERE id_detalle_pedido = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_detalle_pedido
                SET precio = ?, total = ?, cantidad = ?, id_pedido = ?, id_producto = ?
                WHERE id_detalle_pedido = ?';
        $params = array($this->precio, $this->total, $this->cantidad, $this->id_pedido, $this->id_producto, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_detalle_pedido
                WHERE id_detalle_pedido = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
