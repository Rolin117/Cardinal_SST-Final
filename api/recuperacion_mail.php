<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Generar un código único
    $codigo = rand(100000, 999999); // Código de 6 dígitos

    // Guardar el código en la base de datos o en la sesión (ejemplo con sesión)
    session_start();
    $_SESSION['codigo_verificacion'] = $codigo;
    $_SESSION['email_verificacion'] = $email;

    // Enviar el código al correo del usuario
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.tu-servidor.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tu-correo@dominio.com';
        $mail->Password = 'tu-contraseña';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tu-correo@dominio.com', 'Soporte');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación';
        $mail->Body = "Tu código de verificación es: <strong>$codigo</strong>";

        $mail->send();
        echo 'El código ha sido enviado a tu correo';
        header("Location: verificar_codigo.php");
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
