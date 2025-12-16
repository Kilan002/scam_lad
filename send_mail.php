<?php
// Incluye los archivos de PHPMailer (ajusta las rutas según donde los guardes)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $asunto = htmlspecialchars($_POST['asunto']);
    $mensaje = htmlspecialchars($_POST['mensaje']);

    $mail = new PHPMailer(true);

    try {
        // Configuración del Servidor (AJUSTAR ESTO)
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com';   // Ejem: smtp.gmail.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu_correo@example.com'; // Tu cuenta de correo SMTP
        $mail->Password   = 'tu_contraseña';        // Tu contraseña de correo SMTP (o App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // O ENCRYPTION_STARTTLS
        $mail->Port       = 465; // O 587 si usas STARTTLS
        
        // Destinatarios
        $mail->setFrom($email, $nombre);
        $mail->addAddress('correo_de_destino@scamlad.com', 'Administrador Scam LAD'); // A dónde llega el mensaje
        $mail->addReplyTo($email, $nombre);
        
        // Contenido
        $mail->isHTML(false); // No usar HTML en el cuerpo
        $mail->Subject = "Contacto Web: " . $asunto;
        $mail->Body    = "De: $nombre ($email)\n\n" . $mensaje;

        $mail->send();
        echo "Mensaje enviado exitosamente. Redirigiendo...";
        header("refresh:3;url=contact.html?status=success");
        exit();

    } catch (Exception $e) {
        echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
        header("refresh:5;url=contact.html?status=error");
        exit();
    }
}
?>