<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla servicios.
 */
class ServicioHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $id_admin = null;

    /*
     *  Métodos para realizar las operaciones CRUD (create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM tb_servicios
                WHERE nombre_servicio LIKE ? OR descripcion_servicio LIKE ?
                ORDER BY nombre_servicio';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $this->id_admin = $_SESSION['id_administrador'];

        $sql = 'INSERT INTO tb_servicios(nombre_servicio, descripcion_servicio, id_admin)
                VALUES(?, ?, ?)';
        $params = array($this->nombre, $this->descripcion, $this->id_admin);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM tb_servicios
                ORDER BY nombre_servicio';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_servicio, nombre_servicio, descripcion_servicio
                FROM tb_servicios
                WHERE id_servicio = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_servicios
                SET nombre_servicio = ?, descripcion_servicio = ?
                WHERE id_servicio = ?';
        $params = array($this->nombre, $this->descripcion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_servicios
                WHERE id_servicio = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function serviciosPorAdministrador()
    {
        $sql = 'SELECT 
                    a.nombre_admin AS nombre_administrador,
                    a.apellido_admin AS apellido_administrador,
                    s.nombre_servicio
                FROM 
                    tb_servicios s
                INNER JOIN  
                    tb_administradores a 
                ON 
                    s.id_admin = a.id_administrador
                ORDER BY 
                    a.nombre_admin, a.apellido_admin';
        return Database::getRows($sql);
    }


    /*Graficos*/
    public function graficoServicios()
    {
        $sql = "SELECT 
    CONCAT(a.nombre_admin, ' ', a.apellido_admin) AS administrador, 
    COUNT(s.id_servicio) AS total_servicios
FROM 
    tb_administradores a
LEFT JOIN 
    tb_servicios s ON a.id_administrador = s.id_admin
GROUP BY 
    administrador;
";
        return Database::getRows($sql);
    }
}
