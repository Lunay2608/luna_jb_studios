<?php
include("conexion.php");

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
        // send notification email to site owner (Gmail) using PHP mail()
        $to = "angelbenavente2608@gmail.com"; // change to your Gmail address
        $emailSubject = "Nuevo mensaje de contacto";
        $emailBody  = "Has recibido un nuevo mensaje de contacto:\n";
        $emailBody .= "Nombre: $nombre\n";
        $emailBody .= "Email: $email\n";
        $emailBody .= "Asunto: $asunto\n\n";
        $emailBody .= "Mensaje:\n$mensaje\n";

        $headers  = "From: $email" . "\r\n";
        $headers .= "Reply-To: $email" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // note: when running on localhost you must configure SMTP settings in php.ini
        // or use a library like PHPMailer to send via Gmail SMTP with authentication.
        @mail($to, $emailSubject, $emailBody, $headers);

        header("Location: gracias.html");
        exit;
    } else {
        echo "Error al guardar el mensaje.";
    }

    $stmt->close();
    $conexion->close();
}
?>
