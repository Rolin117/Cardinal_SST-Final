<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/servicios_handler.php');

/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla SERVICIOS.
 */
class ServicioData extends ServicioHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;

    /*
     *  Métodos para validar y establecer los datos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del servicio es incorrecto';
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 100)
    {
        if (Validator::validateAlphanumeric($value) && Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe ser alfanumérico y tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 255)
    {
        if (Validator::validateString($value) && Validator::validateLength($value, $min, $max)) {
            $this->descripcion = $value;
            return true;
        } else {
            $this->data_error = 'La descripción contiene caracteres no válidos o no cumple con la longitud permitida';
            return false;
        }
    }

    public function setIdAdmin($id_admin)
    {
        if (Validator::validateNaturalNumber($id_admin)) {
            $this->id_admin = $id_admin;
            return true;
        } else {
            return false;
        }
    }

    /*
     *  Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }
}
?>
