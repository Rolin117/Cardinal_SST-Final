<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla productos.
 */
class ProductoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $precio = null;
    protected $descripcion = null;
    protected $cantidad = null;
    protected $imagen = null;
    protected $id_categoria = null;
    protected $id_admin = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/productos/';

    /*
     *  Métodos para realizar las operaciones CRUD (create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_producto , imagen_producto, nombre_producto, descripcion, precio_producto, nombre_cat
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                WHERE nombre_producto LIKE ? OR descripcion LIKE ?
                ORDER BY nombre_producto';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_productos(nombre_producto, precio_producto, descripcion, cantidad_producto, imagen_producto, id_categoria)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->precio, $this->descripcion, $this->cantidad, $this->imagen, $this->id_categoria);
        return Database::executeRow($sql, $params);
    }


    public function readAll()
    {
        $sql = 'SELECT 
                    p.id_producto, 
                    p.imagen_producto, 
                    p.nombre_producto, 
                    p.descripcion, 
                    CASE 
                        WHEN o.descuento IS NOT NULL THEN ROUND(p.precio_producto - (p.precio_producto * o.descuento / 100), 2)
                        ELSE p.precio_producto 
                    END AS precio_producto,
                    c.nombre_cat
                FROM 
                    tb_productos p
                INNER JOIN 
                    tb_categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN 
                    tb_ofertas o ON p.id_producto = o.id_producto
                ORDER BY 
                    p.nombre_producto';
        return Database::getRows($sql);
    }


    public function readOne()
    {
        $sql = 'SELECT id_producto, nombre_producto, precio_producto, descripcion, cantidad_producto, imagen_producto, id_categoria
                FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_productos
                SET nombre_producto = ?, precio_producto = ?, descripcion = ?, cantidad_producto = ?, imagen_producto = ?, id_categoria = ?
                WHERE id_producto = ?';
        $params = array($this->nombre, $this->precio, $this->descripcion, $this->cantidad, $this->imagen, $this->id_categoria, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen_producto
                FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /* Funciones de reportes */

    public function productosCategoria()
    {
        $sql = 'SELECT nombre_producto, precio_producto, cantidad_producto
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                WHERE id_categoria = ?
                ORDER BY nombre_producto';
        $params = array($this->id_categoria);
        return Database::getRows($sql, $params);
    }

    public function productosAgotados()
    {
        $sql = 'SELECT * FROM tb_productos WHERE cantidad_producto = 0';
        return Database::getRows($sql); // Cambié a getRows para retornar un array de resultados
    }
    

    public function productosAdmin()
    {
        $sql = 'SELECT 
                    a.nombre_admin AS nombre_administrador,
                    a.apellido_admin AS apellido_administrador,
                    p.nombre_producto,
                    p.precio_producto
                FROM 
                    tb_productos p
                INNER JOIN 
                    tb_administradores a 
                ON 
                    p.id_admin = a.id_administrador
                ORDER BY 
                    a.nombre_admin, a.apellido_admin;
                ';
        return Database::getRows($sql);
    }

    public function prediccionProductosDemanda()
{
    $sql = "SELECT 
                p.nombre_producto, 
                p.precio_producto, 
                o.nombre_oferta, 
                SUM(v.cantidad_vendida) AS cantidad_total_vendida,
                COUNT(v.id_venta) AS veces_en_oferta
            FROM 
                tb_productos p
            INNER JOIN 
                tb_ofertas o ON p.id_producto = o.id_producto
            INNER JOIN 
                tb_ventas v ON p.id_producto = v.id_producto
            WHERE 
                v.fecha_venta BETWEEN CURDATE() - INTERVAL 1 MONTH AND CURDATE()
            GROUP BY 
                p.nombre_producto, o.nombre_oferta
            HAVING 
                cantidad_total_vendida > (SELECT AVG(cantidad_vendida) FROM tb_ventas)
            ORDER BY 
                cantidad_total_vendida DESC;
            ";

    return Database::getRows($sql);
}


}
