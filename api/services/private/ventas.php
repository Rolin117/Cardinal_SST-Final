<?php
// Se incluye la clase del modelo.
require_once('../../models/data/ventas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondientea.
    $ventas = new VentasData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['id_administrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'getVentasTotalesPorFecha':
                $result['dataset'] = $ventas->getVentasTotalesPorFecha();
                if ($result['dataset'] !== false) {
                    $result['status'] = 1;
                    $result['message'] = 'Estadísticas de ventas obtenidas correctamente';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'No se pudieron obtener las ventas';
                }
                break;
            case 'ventasPorProducto':
                $result['dataset'] = $ventas->ventasPorProducto();
                if ($result['dataset'] !== false) {
                    $result['status'] = 1;
                    $result['message'] = 'Estadísticas de ventas obtenidas correctamente';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'No se pudieron obtener las ventas';
                }
                break;
            case 'VentasMensualesP':
                if ($result['dataset'] = $ventas->predictVentasMensuales()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen' . count($result['dataset']) . 'registros';
                } else {
                    $result['status'] = 0;
                    $result['error'] = 'No se pudieron obtener las ventas';
                }
                break;
                case 'InventarioProductosP':
                    if ($result['dataset'] = $ventas->InventarioProductosP()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen' . count($result['dataset']) . 'registros';
                    } else {
                        $result['status'] = 0;
                        $result['error'] = 'No se pudieron obtener las ventas';
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
