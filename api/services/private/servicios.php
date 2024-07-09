<?php
// Se incluye la clase del modelo.
require_once('../../models/data/servicios_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();

    // Se instancia la clase correspondiente.
    $servicio = new ServicioData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['id_administrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $servicio->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$servicio->setNombre($_POST['nombre_servicio']) or
                    !$servicio->setDescripcion($_POST['descripcion_servicio'])
                ) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'servicio creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el servicio';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $servicio->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen servicios registrados';
                }
                break;
            case 'readOne':
                if (!$servicio->setId($_POST['id_servicio'])) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($result['dataset'] = $servicio->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'servicio inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$servicio->setId($_POST['id_servicio']) or
                    !$servicio->setNombre($_POST['nombre_servicio']) or
                    !$servicio->setDescripcion($_POST['descripcion_servicio']) 
                ) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'servicio modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el servicio';
                }
                break;
            case 'deleteRow':
                if (
                    !$servicio->setId($_POST['id_servicio'])
                ) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'servicio eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el servicio';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';

        }
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
