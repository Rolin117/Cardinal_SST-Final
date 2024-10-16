<?php
// Se incluye la clase del modelo.
require_once('../../models/data/pedidos_data.php');


// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondientea.
    $pedido = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['id_administrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readOne':
                if (!$pedido->setId($_POST['id_pedido'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($result['dataset'] = $pedido->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'oferta inexistente';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $pedido->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen pedidos registrados';
                }
                break;
                case 'searchRows':
                    if (!Validator::validateSearch($_POST['search'])) {
                        $result['error'] = Validator::getSearchError();
                    } elseif ($result['dataset'] = $pedido->searchRows()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                    } else {
                        $result['error'] = 'No hay coincidencias';
                    }
                    break; 
            case 'pedidosPorClienteG':
                $result['dataset'] = $pedido->pedidosPorClienteG();
                if ($result['dataset'] !== false) {
                    $result['status'] = 1;
                    $result['message'] = 'Estadísticas del pedido obtenidas correctamente';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'No se pudieron obtener los pedidos';
                }
                break;
                case 'getClientePorPedido':
                    if (!$pedido->setId($_POST['id_pedido'])) {
                        $result['error'] = 'ID de pedido inválido';
                    } elseif ($result['dataset'] = $pedido->getClientePorPedido()) {
                        $result['status'] = 1;
                        $result['message'] = 'Información del cliente obtenida correctamente';
                    } else {
                        $result['error'] = 'No se pudo obtener la información del cliente';
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
