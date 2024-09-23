<?php
// Se incluye la clase del modelo.
require_once('../../models/data/administrador_data.php');
require_once('../../models/data/clientes_data.php');


// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();

    $limiteInactividad = 300; // 5 * 60 = 300 segundos

    // Verificar si hay tiempo de actividad previo
    if (isset($_SESSION['ultimoTiempoActividad'])) {
        $tiempoInactivo = time() - $_SESSION['ultimoTiempoActividad'];

        // Si el tiempo de inactividad excede el límite, cerrar sesión
        if ($tiempoInactivo > $limiteInactividad) {
            session_unset(); // Limpia las variables de sesión
            session_destroy(); // Destruye la sesión
            $result['status'] = 0;
            $result['message'] = 'Sesión cerrada por inactividad';
            header('Content-type: application/json; charset=utf-8');
            print(json_encode($result));
            exit();
        }
    }

    // Actualizar el tiempo de actividad
    $_SESSION['ultimoTiempoActividad'] = time();

    // Se instancia la clase correspondiente.
    $administrador = new AdministradorData;
    $cliente = new ClientesData;
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se procede según las acciones disponibles.
    if (isset($_SESSION['id_administrador'])) {
        $result['session'] = 1;

        // Switch para manejar las acciones cuando hay una sesión activa de administrador.
        switch ($_GET['action']) {
            case 'deleteRowC': // Agrega esta línea
                if (!$cliente->setId($_POST['id_cliente'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el cliente';
                }
                break;
            case 'updateRowC':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setId($_POST['id_cliente']) ||
                    !$cliente->setNombre($_POST['nombre_cliente']) ||
                    !$cliente->setApellido($_POST['apellido_cliente']) ||
                    !$cliente->setCorreo($_POST['correo_cliente']) ||
                    !$cliente->setTelefono($_POST['telefono_cliente']) ||
                    !$cliente->setClave($_POST['contrasenia_cliente'])

                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el cliente';
                }
                break;
            case 'readOneC':
                if (!$cliente->setId($_POST['id_cliente'])) {
                    $result['error'] = 'Cliente incorrecto';
                } elseif ($result['dataset'] = $cliente->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Cliente inexistente';
                }
                break;
            case 'readAllC':
                if ($result['dataset'] = $cliente->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen clientes registrados';
                }
                break;
            case 'searchRowsC':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $cliente->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;










            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $administrador->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombre_admin']) ||
                    !$administrador->setApellido($_POST['apellido_admin']) ||
                    !$administrador->setCorreo($_POST['correo_admin']) ||
                    !$administrador->setTelefono($_POST['telefono_admin']) ||
                    !$administrador->setClave($_POST['contrasenia_admin'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['contrasenia_admin'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el administrador';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $administrador->readAll()) {
                    $result['status'] = 1;
                    $result['session_admin_id'] = $_SESSION['id_administrador']; // Añadir el ID del administrador de la sesión
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen administradores registrados';
                }
                break;
            case 'readOne':
                if (!$administrador->setId($_POST['id_administrador'])) {
                    $result['error'] = 'Administrador incorrecto';
                } elseif ($result['dataset'] = $administrador->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Administrador inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setId($_POST['id_administrador']) ||
                    !$administrador->setNombre($_POST['nombre_admin']) ||
                    !$administrador->setApellido($_POST['apellido_admin']) ||
                    !$administrador->setCorreo($_POST['correo_admin']) ||
                    !$administrador->setTelefono($_POST['telefono_admin']) ||
                    !$administrador->setClave($_POST['contrasenia_admin'])

                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el administrador';
                }
                break;
            case 'deleteRow':
                if ($_POST['id_administrador'] == $_SESSION['id_administrador']) {
                    $result['error'] = 'No se puede eliminar a sí mismo';
                } elseif (!$administrador->setId($_POST['id_administrador'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el administrador';
                }
                break;
            case 'getUser':
                if (isset($_SESSION['correo_admin'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['correo_admin'];
                } else {
                    $result['error'] = 'Correo de administrador indefinido';
                }
                break;
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;

                break;
            case 'readProfile':
                if ($result['dataset'] = $administrador->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombre_admin']) or
                    !$administrador->setApellido($_POST['apellido_admin']) or
                    !$administrador->setCorreo($_POST['correo_admin']) or
                    !$administrador->setTelefono($_POST['telefono_admin'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                    $_SESSION['nombre_admin'] = $_POST['nombre_admin'];
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el perfil';
                }
                break;
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$administrador->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$administrador->setClave($_POST['claveNueva'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
                break;
        }
    } else {
        // Switch para manejar las acciones cuando no hay una sesión activa de administrador.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($administrador->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un administrador para comenzar';
                }
                break;
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if ($administrador->readAll()) {
                    $result['error'] = 'Ya existe al menos un usuario registrado';
                } elseif (
                    !$administrador->setNombre($_POST['nombre_admin']) ||
                    !$administrador->setApellido($_POST['apellido_admin']) ||
                    !$administrador->setCorreo($_POST['correo_admin']) ||
                    !$administrador->setTelefono($_POST['telefono_admin']) ||
                    !$administrador->setClave($_POST['contrasenia_admin'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['contrasenia_admin'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar el administrador';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if ($administrador->checkUser($_POST['correo_admin'], $_POST['contrasenia_admin'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'Credenciales incorrectas';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
                break;
        }
    }

    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();

    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');

    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
