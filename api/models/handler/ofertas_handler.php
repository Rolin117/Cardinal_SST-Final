<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *	Clase para manejar el comportamiento de los datos de la tabla OFERTAS.
 */
class OfertaHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    protected $id_oferta = null;
    protected $nombre_oferta = null;
    protected $descripcion_oferta = null;
    protected $descuento = null;
    protected $id_producto = null;
    protected $fecha_inicio = null;
    protected $fecha_fin = null;


    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT o.id_oferta, o.nombre_oferta, o.descripcion_oferta, o.descuento, p.id_producto, p.nombre_producto
                FROM tb_ofertas o
                INNER JOIN tb_productos p ON o.id_producto = p.id_producto
                WHERE o.nombre_oferta LIKE ? OR p.nombre_producto LIKE ?
                ORDER BY o.nombre_oferta';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        // Verificar el estado actual de 'hasDiscount' para el producto específico
        $sqlCheck = 'SELECT hasDiscount FROM tb_productos WHERE id_producto = ?';
        $paramsCheck = array($this->id_producto);
        $checkResult = Database::getRow($sqlCheck, $paramsCheck);

        // Si hasDiscount ya es 1, no permitimos crear el row y retornamos false
        if ($checkResult && $checkResult['hasDiscount'] == 1) {
            return false;
        } else {
            // Si hasDiscount no es 1, procedemos con la actualización y la inserción
            $sqlUpdate = 'UPDATE tb_productos SET hasDiscount = 1 WHERE id_producto = ?';
            $paramsUpdate = array($this->id_producto);
            Database::executeRow($sqlUpdate, $paramsUpdate);

            // Ahora procedemos con la inserción en tb_ofertas, incluyendo las fechas
            $sqlInsert = 'INSERT INTO tb_ofertas (nombre_oferta, descripcion_oferta, descuento, id_producto, fecha_inicio, fecha_fin)
                          VALUES (?, ?, ?, ?, ?, ?)';
            $paramsInsert = array($this->nombre_oferta, $this->descripcion_oferta, $this->descuento, $this->id_producto, $this->fecha_inicio, $this->fecha_fin);
            return Database::executeRow($sqlInsert, $paramsInsert);
        }
    }


    public function readOne()
    {
        $sql = 'SELECT id_oferta, nombre_oferta, descripcion_oferta, descuento, id_producto, fecha_inicio, fecha_fin
                FROM tb_ofertas
                WHERE id_oferta = ?';
        $params = array($this->id_oferta);
        return Database::getRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT o.id_oferta, o.nombre_oferta, o.descripcion_oferta, o.descuento, p.nombre_producto, o.fecha_inicio, o.fecha_fin
                FROM tb_ofertas o
                INNER JOIN tb_productos p ON o.id_producto = p.id_producto
                ORDER BY o.nombre_oferta';
        return Database::getRows($sql);
    }


    public function updateRow()
    {
        $sql = 'UPDATE tb_ofertas
                SET nombre_oferta = ?, descripcion_oferta = ?, descuento = ?, id_producto = ?, fecha_inicio = ?, fecha_fin = ?
                WHERE id_oferta = ?';
        $params = array($this->nombre_oferta, $this->descripcion_oferta, $this->descuento, $this->id_producto, $this->fecha_inicio, $this->fecha_fin, $this->id_oferta);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        // Actualizar hasDiscount a 0 en tb_productos
        $sqlUpdate = 'UPDATE tb_productos SET hasDiscount = 0 WHERE id_producto = (SELECT id_producto FROM tb_ofertas WHERE id_oferta = ?)';
        $paramsUpdate = array($this->id_oferta);
        Database::executeRow($sqlUpdate, $paramsUpdate);

        // Primero, elimina la oferta en tb_ofertas
        $sqlDelete = 'DELETE FROM tb_ofertas WHERE id_oferta = ?';
        $paramsDelete = array($this->id_oferta);
        return Database::executeRow($sqlDelete, $paramsDelete);
    }

    public function OfertasProductos()
    {
        $sql = 'SELECT 
                p.nombre_producto, 
                p.precio_producto, 
                o.nombre_oferta, 
                o.descuento
                FROM 
                    tb_productos p
                INNER JOIN 
                    tb_ofertas o 
                ON 
                p.id_producto = o.id_producto;
                ';
        return Database::getRows($sql);
    }

    /*Graficos*/
    public function OfertasProductosA()
    {
        $sql = 'SELECT 
    p.nombre_producto, 
    COUNT(o.id_oferta) AS total_ofertas
FROM 
    tb_productos p
LEFT JOIN 
    tb_ofertas o ON p.id_producto = o.id_producto
GROUP BY 
    p.nombre_producto;
';
        return Database::getRows($sql);
    }


    /*a*/
}
