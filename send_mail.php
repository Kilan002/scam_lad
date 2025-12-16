<?php
// Incluye los archivos de PHPMailer (ajusta las rutas según donde los guardes)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Modo depuración (activar solo en entorno de desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Intentar carga por Composer si existe, si no buscar instalación manual
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    $pmBase = __DIR__ . '/PHPMailer/src/';
    $required = [
        $pmBase . 'Exception.php',
        $pmBase . 'PHPMailer.php',
        $pmBase . 'SMTP.php'
    ];

    $missing = array_filter($required, function($f){ return !file_exists($f); });
    if (!empty($missing)) {
        http_response_code(500);
        echo "Error 500 — PHPMailer no encontrado. Archivos faltantes:<br>" . implode('<br>', $missing);
        exit();
    }

    require $pmBase . 'Exception.php';
    require $pmBase . 'PHPMailer.php';
    require $pmBase . 'SMTP.php';
}

// Solo procesar POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
    $asunto = isset($_POST['asunto']) ? htmlspecialchars($_POST['asunto']) : '(sin asunto)';
    $mensaje = isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : '';

    if (!$email) {
        http_response_code(400);
        echo "Email no válido.";
        exit();
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración del Servidor (AJUSTAR ESTO a tus credenciales SMTP)
        $smtpHost = 'smtp.gmail.com';
        $smtpUser = 'luis.prz.rosales@gmail.com';
        $smtpPass = 'gnww axtk olzy gqxs';
        $smtpPort = 465; // 587 para STARTTLS
        $smtpSecure = PHPMailer::ENCRYPTION_SMTPS; // o PHPMailer::ENCRYPTION_STARTTLS

        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = $smtpSecure;
        $mail->Port       = $smtpPort;

        // Destinatarios — usar la cuenta SMTP como From para evitar rechazos
        $mail->setFrom($smtpUser, 'Contacto Web');
        $mail->addAddress('luis.prz.rosales@gmail.com', 'Administrador Scam LAD');
        $mail->addReplyTo($email, $nombre);

        // Contenido
        $mail->isHTML(false);
        $mail->Subject = "Contacto Web: " . $asunto;
        $mail->Body    = "De: $nombre ($email)\n\n" . $mensaje;

        $mail->send();
        // Respuesta simple: texto plano y código 200
        http_response_code(200);
        echo "OK";
        exit();

    } catch (Exception $e) {
        http_response_code(500);
        // Mensaje de error corto (útil para debugging rápido)
        echo "ERROR: " . htmlspecialchars($mail->ErrorInfo ?: $e->getMessage());
        exit();
    }
} else {
    // No método POST — mostrar mensaje breve
    http_response_code(405);
    echo "Method Not Allowed";
    exit();
}
?>