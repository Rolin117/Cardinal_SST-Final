<?php
// Se incluye la clase del modelo.
require_once('../../models/data/ofertas_data.php');



// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $ofertas = new OfertaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['id_administrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $ofertas->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$ofertas->setTitulo($_POST['nombre_oferta']) or
                    !$ofertas->setDescripcion($_POST['descripcion_oferta']) or
                    !$ofertas->setDescuento($_POST['descuento']) or
                    !$ofertas->setProducto($_POST['ofertaProducto'])
                ) {
                    $result['error'] = $ofertas->getDataError();
                } elseif ($ofertas->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'oferta creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la oferta';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $ofertas->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen ofertas registradas';
                }
                break;
            case 'readOne':
                if (!$ofertas->setIdO($_POST['id_oferta'])) {
                    $result['error'] = $ofertas->getDataError();
                } elseif ($result['dataset'] = $ofertas->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'oferta inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$ofertas->setIdO($_POST['id_oferta']) or
                    !$ofertas->setTitulo($_POST['nombre_oferta']) or
                    !$ofertas->setDescripcion($_POST['descripcion_oferta']) or
                    !$ofertas->setDescuento($_POST['descuento']) or
                    !$ofertas->setProducto($_POST['ofertaProducto'])
                ) {
                    $result['error'] = $ofertas->getDataError();
                } elseif ($ofertas->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'oferta modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el oferta';
                }
                break;
            case 'deleteRow':
                if (
                    !$ofertas->setIdO($_POST['id_oferta'])
                ) {
                    $result['error'] = $ofertas->getDataError();
                } elseif ($ofertas->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'oferta eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la oferta';
                }
                break;
                case 'OfertasProductosA':
                    $result['dataset'] = $ofertas->OfertasProductosA();
                    if ($result['dataset'] !== false) {
                        $result['status'] = 1;
                        $result['message'] = 'Estadísticas de ofertas por producto obtenidas correctamente';
                    } else {
                        $result['status'] = 0;
                        $result['error'] = 'No se pudieron obtener las estadísticas de ofertas por producto';
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
