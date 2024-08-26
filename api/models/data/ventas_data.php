<?php

require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/ventas_handler.php');

class VentasData extends ventaHandler
{
    private $data_error = null;

    public function getDataError()
    {
        return $this->data_error;
    }
}


?>