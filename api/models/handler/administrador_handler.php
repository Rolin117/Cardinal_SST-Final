<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class AdministradorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $telefono = null;
    protected $clave = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, correo_admin, contrasenia_admin
                FROM tb_administradores
                WHERE correo_admin = ?';
        $params = array($username);
        if (!($data = Database::getRow($sql, $params))) {
            return false;
        } elseif (password_verify($password, $data['contrasenia_admin'])) {
            $_SESSION['id_administrador'] = $data['id_administrador'];
            $_SESSION['correo_admin'] = $data['correo_admin'];
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT contrasenia_admin
                FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($_SESSION['id_administrador']);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['contrasenia_admin'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE tb_administradores
                SET contrasenia_admin = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['id_administrador']);
        return Database::executeRow($sql, $params);
    }


    public function editProfile()
    {
        $sql = 'UPDATE tb_administradores
                SET nombre_admin = ?, apellido_admin = ?, correo_admin = ?, telefono_admin = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->telefono, $_SESSION['id_administrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_admin, apellido_admin, correo_admin, telefono_admin
                FROM tb_administradores
                WHERE nombre_admin LIKE ?
                ORDER BY apellido_admin';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_administradores(nombre_admin, apellido_admin, correo_admin, telefono_admin, contrasenia_admin)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->telefono, $this->clave);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_administrador, nombre_admin, apellido_admin, correo_admin, telefono_admin
                FROM tb_administradores
                ORDER BY apellido_admin';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_administrador, nombre_admin, apellido_admin, correo_admin, telefono_admin
                FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_administradores
                SET nombre_admin = ?, apellido_admin = ?, correo_admin = ?, telefono_admin = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->telefono, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
