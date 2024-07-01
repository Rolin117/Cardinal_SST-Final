<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla OFERTAS.
 */
class OfertaHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    protected $id= null;
    protected $titulo = null;
    protected $descripcion = null;  
    protected $descuento = null;
    protected $producto = null;

    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_oferta, titulo, descripcion, descuento, idProducto, nombreProducto
        from tb_ofertas
        INNER JOIN tb_productos USING(idProducto)
        WHERE titulo LIKE ? OR nombreProducto LIKE ?
        ORDER BY titulo';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        // Primero, verificamos el estado actual de 'hasDiscount' para el producto específico
        $sqlCheck = 'SELECT hasDiscount FROM tb_productos WHERE idProducto = ?';
        $paramsCheck = array($this->producto);
        $checkResult = Database::getRow($sqlCheck, $paramsCheck); // Asume que getRow retorna la fila correspondiente o null si no encuentra nada

        // Si hasDiscount ya es 1, no permitimos crear el row y retornamos false
        if ($checkResult && $checkResult['hasDiscount'] == 1) {
            return false;
        } else {
            // Si hasDiscount no es 1, procedemos con la actualización y la inserción
            $sqlUpdate = 'UPDATE tb_productos SET hasDiscount = 1 WHERE idProducto = ?';
            $paramsUpdate = array($this->producto);
            Database::executeRow($sqlUpdate, $paramsUpdate);

            // Ahora procedemos con la inserción en tb_ofertas
            $sqlInsert = 'INSERT INTO tb_ofertas(titulo, descripcion, descuento, idProducto)
                      VALUES(?, ?, ?, ?)';
            $paramsInsert = array($this->titulo, $this->descripcion, $this->descuento, $this->producto);
            return Database::executeRow($sqlInsert, $paramsInsert);
        }
    }

    public function readAll()
    {
        $sql = 'SELECT id_oferta, titulo, descripcion, descuento, nombreProducto
                FROM tb_ofertas
                INNER JOIN tb_productos USING(idProducto)
                ORDER BY titulo';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_oferta, titulo, descripcion, descuento, idProducto
                FROM tb_ofertas
                WHERE id_oferta = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_ofertas
                SET titulo = ?, descripcion = ?, descuento = ?, idProducto = ?
                WHERE id_oferta = ?';
        $params = array($this->titulo, $this->descripcion, $this->descuento, $this->producto, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        // Actualizar hasDiscount a 0 en tb_productos
        $sqlUpdate = 'UPDATE tb_productos SET hasDiscount = 0 WHERE idProducto = (SELECT idProducto FROM tb_ofertas WHERE id_oferta = ?)';
        $paramsUpdate = array($this->id);
        Database::executeRow($sqlUpdate, $paramsUpdate);


        // Primero, elimina la oferta en tb_ofertas
        $sqlDelete = 'DELETE FROM tb_ofertas WHERE id_oferta = ?';
        $paramsDelete = array($this->id);
        return Database::executeRow($sqlDelete, $paramsDelete);
    }

}
