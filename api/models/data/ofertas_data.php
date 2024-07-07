<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/ofertas_handler.php');

/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla OFERTAS.
 */
class OfertaData extends OfertaHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;

    /*
     *  Métodos para validar y establecer los datos.
     */
    public function setIdO($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la oferta es incorrecto';
            return false;
        }
    }

    public function setTitulo($value, $min = 2, $max = 100)
    {
        if (Validator::validateAlphanumeric($value) && Validator::validateLength($value, $min, $max)) {
            $this->nombre_oferta = $value;
            return true;
        } else {
            $this->data_error = 'El título debe ser alfanumérico y tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 255)
    {
        if (Validator::validateString($value) && Validator::validateLength($value, $min, $max)) {
            $this->descripcion_oferta = $value;
            return true;
        } else {
            $this->data_error = 'La descripción contiene caracteres no válidos o no cumple con la longitud permitida';
            return false;
        }
    }

    public function setDescuento($value)
    {
        if (Validator::validateMoney($value)) {
            $this->descuento = $value;
            return true;
        } else {
            $this->data_error = 'El descuento no es válido';
            return false;
        }
    }

    public function setProducto($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->producto = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del producto es incorrecto';
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
