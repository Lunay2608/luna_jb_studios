<?php
include("conexion.php");
include("config_email.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/phpmailer/Exception.php';
require 'lib/phpmailer/PHPMailer.php';
require 'lib/phpmailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre  = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $asunto  = trim($_POST["subject"]);
    $mensaje = trim($_POST["message"]);

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        echo "Todos los campos obligatorios deben llenarse.";
        exit;
    }

    $stmt = $conexion->prepare(
        "INSERT INTO contacto_mensajes (nombre, email, asunto, mensaje)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $nombre, $email, $asunto, $mensaje);

    if ($stmt->execute()) {
        // Enviar correo usando PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = GMAIL_USER;
            $mail->Password   = GMAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(GMAIL_USER, GMAIL_FROM_NAME);
            $mail->addAddress(GMAIL_USER);
            $mail->addReplyTo($email, $nombre);

            $mail->isHTML(false);
            $mail->Subject = "Nuevo mensaje de contacto";
            $mail->Body    = "Has recibido un nuevo mensaje:\n\n"
                           . "Nombre: $nombre\n"
                           . "Email: $email\n"
                           . "Asunto: $asunto\n\n"
                           . "Mensaje:\n$mensaje";

            $mail->send();
            
            header("Location: gracias.html");
            exit;
        } catch (Exception $e) {
            echo "Error al enviar el correo: " . $mail->ErrorInfo;
        }
    } else {
        echo "Error al guardar el mensaje.";
    }

    $stmt->close();
    $conexion->close();
}
?>
