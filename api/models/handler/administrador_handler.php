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
        // Consulta para obtener el administrador
        $sql = 'SELECT id_administrador, correo_admin, contrasenia_admin, intentos_fallidos, ultimo_intento
                FROM tb_administradores
                WHERE correo_admin = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
    
        // Verificar si el administrador existe
        if (!$data) {
            return ['status' => false, 'message' => 'Usuario no encontrado.'];
        }
    
        // Verificar si la cuenta está bloqueada
        if ($data['intentos_fallidos'] >= 3) {
            $tiempoBloqueo = new DateTime($data['ultimo_intento']);
            $tiempoActual = new DateTime();
            $diferencia = $tiempoActual->diff($tiempoBloqueo);
    
            if ($diferencia->i < 1440) {
                return ['status' => false, 'message' => 'La cuenta está bloqueada. Intenta nuevamente más tarde.'];
            } else {
                // Reiniciar intentos fallidos si han pasado más de 15 minutos
                $this->resetIntentos($data['id_administrador']);
            }
        }
    
        // Verificar la contraseña
        if (password_verify($password, $data['contrasenia_admin'])) {
            $this->resetIntentos($data['id_administrador']);
            $_SESSION['id_administrador'] = $data['id_administrador'];
            $_SESSION['correo_admin'] = $data['correo_admin'];
            return ['status' => true, 'message' => 'Autenticación correcta.'];
        } else {
            $this->incrementarIntentos($data['id_administrador']);
            return ['status' => false, 'message' => 'Credenciales incorrectas.'];
        }
    }
    

    // Método para incrementar intentos fallidos
    public function incrementarIntentos($id_admin)
    {
        $sql = "UPDATE tb_administradores SET intentos_fallidos = intentos_fallidos + 1, ultimo_intento = NOW() WHERE id_administrador = ?";
        $params = array($id_admin);
        Database::executeRow($sql, $params);
    }

    // Método para reiniciar intentos fallidos
    public function resetIntentos($id_admin)
    {
        $sql = "UPDATE tb_administradores SET intentos_fallidos = 0 WHERE id_administrador = ?";
        $params = array($id_admin);
        Database::executeRow($sql, $params);
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

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_admin, apellido_admin, correo_admin, telefono_admin
                FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($_SESSION['id_administrador']);
        return Database::getRow($sql, $params);
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
                SET nombre_admin = ?, apellido_admin = ?, correo_admin = ?, telefono_admin = ?, contrasenia_admin = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->telefono, $this->clave, $this->id);
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
