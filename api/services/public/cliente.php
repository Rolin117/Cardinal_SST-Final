<?php
// Se incluye la clase del modelo.
require_once('../../models/data/clientes_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $cliente = new ClientesData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'recaptcha' => 0, 'message' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['id_cliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            case 'getUser':
                if (isset($_SESSION['correoCliente'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['correoCliente'];
                    // $result['name'] = $cliente->readOneCorreo($_SESSION['nombreCliente']);
                } else {
                    $result['error'] = 'Correo de usuario indefinido';
                }
                break;
            case 'readProfile':
                if ($result['dataset'] = $cliente->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombre_perfil']) or
                    !$cliente->setApellido($_POST['apellido_perfil']) or
                    !$cliente->setCorreo($_POST['correo_perfil']) or
                    !$cliente->setTelefono($_POST['telefono_perfil'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                    $_SESSION['correo_perfil'] = $_POST['correo_perfil'];
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el perfil';
                }
                break;
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkPassword($_POST['contra_actual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['contra_reciente'] != $_POST['repetir_contra']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$cliente->setClave($_POST['contra_reciente'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
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

            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setId($_POST['id_cliente']) ||
                    !$cliente->setNombre($_POST['nombre_cliente']) ||
                    !$cliente->setApellido($_POST['apellido_cliente']) ||
                    !$cliente->setCorreo($_POST['correo_cliente']) ||
                    !$cliente->setTelefono($_POST['telefono_cliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el cliente';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombre_cliente']) or
                    !$cliente->setApellido($_POST['apellido_cliente']) or
                    !$cliente->setCorreo($_POST['correo_cliente']) or
                    !$cliente->setTelefono($_POST['telefono_cliente']) or
                    !$cliente->setClave($_POST['contra_cliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['contra_cliente'] != $_POST['confirmar_contra']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cuenta registrada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar la cuenta';
                }
                break;
            case 'signUpMovil':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombre']) or
                    !$cliente->setApellido($_POST['apellido']) or
                    !$cliente->setCorreo($_POST['correo']) or
                    !$cliente->setTelefono($_POST['telefono']) or
                    !$cliente->setClave($_POST['contrasena'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cuenta registrada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar la cuenta';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if ($cliente->checkUser($_POST['correo'], $_POST['clave'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'Credenciales incorrectas';
                }
                break;
            case 'emailPasswordSender':
                $_POST = Validator::validateForm($_POST);

                if (!$cliente->setCorreo($_POST['correo'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->verifyExistingEmail()) {

                    $secret_change_password_code = mt_rand(10000000, 99999999);
                    $token = Validator::generateRandomString(64);

                    $_SESSION['secret_change_password_code'] = [
                        'code' => $secret_change_password_code,
                        'token' => $token,
                        'expiration_time' => time() + (60 * 15) # (x*y) y=minutos de vida 
                    ];

                    $_SESSION['usuario_correo_vcc'] = [
                        'correo' => $_POST['correo'],
                        'expiration_time' => time() + (60 * 25) # (x*y) y=minutos de vida 
                    ];
                    sendVerificationEmail($_POST['correo'], $secret_change_password_code, 'password_reset');
                    //sendVerificationEmail($_POST['correo'], $secret_change_password_code);
                    $result['status'] = 1;
                    $result['message'] = 'Correo enviado';
                    $result['dataset'] = $token;
                } else {
                    $result['error'] = 'El correo indicado no existe';
                }
                break;
            case 'emailPasswordValidator':
                $_POST = Validator::validateForm($_POST);

                if (!isset($_POST['codigo_secret'])) {
                    $result['error'] = "El código no fue proporcionado";
                } elseif (!isset($_POST["token"])) {
                    $result['error'] = 'El token no fue proporcionado';
                } elseif (!(ctype_digit($_POST['codigo_secret']) && strlen($_POST['codigo_secret']) === 8)) {
                    $result['error'] = "El código es inválido";
                } elseif (!isset($_SESSION['secret_change_password_code'])) {
                    $result['error'] = "El código ha expirado";
                } elseif (!isset($_SESSION['secret_change_password_code']['token'])) {
                    $result['error'] = "El token ha expirado o no fue proporcionado";
                } elseif ($_SESSION['secret_change_password_code']['token'] != $_POST["token"]) {
                    $result['error'] = 'El token es inválido';
                } elseif (!isset($_SESSION['secret_change_password_code']['expiration_time']) || $_SESSION['secret_change_password_code']['expiration_time'] <= time()) {
                    $result['message'] = "El código ha expirado.";
                    unset($_SESSION['secret_change_password_code']);
                } elseif (!isset($_SESSION['secret_change_password_code']['code']) || $_SESSION['secret_change_password_code']['code'] != $_POST['codigo_secret']) {
                    $result['error'] = "El código es incorrecto";
                } else {
                    // Código correcto, generar el token
                    $token = Validator::generateRandomString(64);
                    $_SESSION['secret_change_password_code_validated'] = [
                        'token' => $token,
                        'expiration_time' => time() + (10 * 60) // Expiración de 10 minutos 
                    ];
                    $result['status'] = 1;
                    $result['message'] = "Verificación Correcta";
                    $result['dataset'] = $token;
                    unset($_SESSION['secret_change_password_code']);
                }
                break;

            case 'changePasswordByEmail':
                $_POST = Validator::validateForm($_POST);
                $nombreUsuario = $cliente->getUserNameById($cliente->getIdByEmail($_SESSION['usuario_correo_vcc']['correo']));
                $correoUsuario = $_SESSION['usuario_correo_vcc']['correo'];

                // Verificar si el token fue proporcionado.
                if (!isset($_POST["token"])) {
                    $result['error'] = 'El token no fue proporcionado';

                    // Verificar si la sesión para el cambio de contraseña ha expirado.
                } elseif (!isset($_SESSION['secret_change_password_code_validated'])) {
                    $result['error'] = 'El tiempo para cambiar su contraseña ha expirado';
                } elseif ($_SESSION['secret_change_password_code_validated']['expiration_time'] <= time()) {
                    $result['error'] = 'El tiempo para cambiar su contraseña ha expirado';
                    unset($_SESSION['secret_change_password_code_validated']);

                    // Verificar si el token proporcionado es válido.
                } elseif ($_SESSION['secret_change_password_code_validated']['token'] != $_POST["token"]) {
                    $result['error'] = 'El token es inválido';

                    // Verificar si las contraseñas nuevas coinciden.
                } elseif ($_POST['nuevaClave'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';

                    // Verificar si la nueva contraseña no es igual a la actual.
                } elseif (password_verify($_POST['nuevaClave'], $cliente->getPasswordHash($cliente->getIdByEmail($_SESSION['usuario_correo_vcc']['correo'])))) {
                    $result['error'] = 'La nueva contraseña no puede ser igual a la contraseña actual';

                    // Validar la nueva contraseña con las reglas establecidas.
                } elseif (!Validator::validatePassword($_POST['nuevaClave'], [$nombreUsuario, $_SESSION['usuario_correo_vcc']['correo']], $correoUsuario)) {
                    $result['error'] = Validator::getPasswordError();

                    // Verificar si hubo un error al establecer la nueva contraseña.
                } elseif (!$cliente->setClave($_POST['nuevaClave'])) {
                    $result['error'] = $cliente->getDataError();

                    // Verificar si la sesión del usuario ha expirado.
                } elseif (!isset($_SESSION['usuario_correo_vcc']) || $_SESSION['usuario_correo_vcc']['expiration_time'] <= time()) {
                    $result['error'] = 'El tiempo para cambiar su contraseña ha expirado';
                    unset($_SESSION['usuario_correo_vcc']);

                    // Si todo es correcto, cambiar la contraseña en la base de datos.
                } elseif ($cliente->changePasswordFromEmail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                    unset($_SESSION['secret_change_password_code_validated']);
                    unset($_SESSION['usuario_correo_vcc']);
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
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
