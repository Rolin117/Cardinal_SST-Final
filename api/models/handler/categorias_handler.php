<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla CATEGORIA.
 */
class CategoriaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $imagen = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/categorias/';

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_categoria, nombre_cat, imagen, descripcion_cat
                FROM tb_categorias
                WHERE nombre_cat LIKE ? OR descripcion_cat LIKE ?
                ORDER BY nombre_cat';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_categorias(nombre_cat, imagen, descripcion_cat)
                VALUES(?, ?, ?)';
        $params = array($this->nombre, $this->imagen, $this->descripcion);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_categoria, nombre_cat, imagen, descripcion_cat
                FROM tb_categorias
                ORDER BY nombre_cat';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_categoria, nombre_cat, imagen, descripcion_cat
                FROM tb_categorias
                WHERE id_categoria = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen
                FROM tb_categorias
                WHERE id_categoria = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_categorias
                SET imagen = ?, nombre_cat = ?, descripcion_cat = ?
                WHERE id_categoria = ?';
        $params = array($this->imagen, $this->nombre, $this->descripcion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        // Primero, elimina todos los productos asociados con la categoría
        $sqlProducts = 'DELETE FROM tb_productos WHERE id_categoria = ?';
        Database::executeRow($sqlProducts, array($this->id));

        // Luego, elimina la categoría
        $sqlCategory = 'DELETE FROM tb_categorias WHERE id_categoria = ?';
        return Database::executeRow($sqlCategory, array($this->id));
    }

    public function getCategoriaAdvancedStats()
    {
        $sql = 'SELECT 
                c.id_categoria, 
                c.nombre_cat, 
                COUNT(p.id_producto) AS total_productos,
                MIN(p.precio_producto) AS precio_minimo,
                MAX(p.precio_producto) AS precio_maximo,
                (MAX(p.precio_producto) - MIN(p.precio_producto)) AS rango_precios
            FROM 
                tb_categorias c
            LEFT JOIN 
                tb_productos p ON c.id_categoria = p.id_categoria
            GROUP BY 
                c.id_categoria, c.nombre_cat';

        try {
            $result = Database::getRows($sql);
            if ($result === false) {
                // Si getRows devuelve false, lanza una excepción
                throw new Exception("Error executing query");
            }
            return $result;
        } catch (Exception $e) {
            // Log the error and return false to indicate failure
            error_log('Error in getCategoriaAdvancedStats: ' . $e->getMessage());
            return false;
        }
    }

    public function getCategoriaPrecioBoxplot()
    {
        $sql = 'SELECT 
    c.nombre_cat, 
    p.precio_producto
FROM 
    tb_productos p
INNER JOIN 
    tb_categorias c ON p.id_categoria = c.id_categoria;
        ';
        return Database::getRows($sql);
    }

    public function PrecioCategorias()
    {
        $sql = 'SELECT 
    c.nombre_cat, 
    AVG(p.precio_producto) AS precio_promedio
FROM 
    tb_productos p
INNER JOIN 
    tb_categorias c ON p.id_categoria = c.id_categoria
GROUP BY 
    c.nombre_cat;
';
        return Database::getRows($sql);
    }

    public function productosCategorias()
    {
        $sql = 'SELECT 
    c.nombre_cat, 
    COUNT(p.id_producto) AS total_productos
FROM 
    tb_categorias c
LEFT JOIN 
    tb_productos p ON c.id_categoria = p.id_categoria
GROUP BY 
    c.nombre_cat;
';
return Database::getRows($sql);

    }
}
