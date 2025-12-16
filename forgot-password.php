<?php
session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: welcome.php");
    die();
}

include 'config.php';
$msg = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // Generar un código único
    $code = mysqli_real_escape_string($conn, md5(rand()));

    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
        // Guardar el código en la base de datos
        $query = mysqli_query($conn, "UPDATE users SET code='{$code}' WHERE email='{$email}'");

        if ($query) {
            // --- MODO SIMULACIÓN (Para que funcione sin SMTP) ---
            // En un sistema real, aquí iría el código de PHPMailer.
            // Aquí simplemente mostramos el link en pantalla.
            
            // Detectar automáticamente la URL de tu sitio
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            // Ajustar ruta si estás en subcarpetas
            $path = dirname($_SERVER['PHP_SELF']); 
            
            $link = "$protocol://$host$path/change-password.php?reset=$code";

            $msg = "<div class='alert alert-success'>
                        <b>¡Enlace generado!</b> (Simulación de correo)<br>
                        Haz clic aquí para cambiar tu clave:<br>
                        <a href='$link' style='color: white; font-weight:bold;'>REESTABLECER CONTRASEÑA</a>
                    </div>";
        }
    } else {
        $msg = "<div class='alert alert-error'>Este correo no existe en nuestra base de datos.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar - Scam Lad</title>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ESTILO DARK HACKER (Igual que login/register) */
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0; background-color: #050505;
            background-image: radial-gradient(#1a0000 1px, transparent 1px);
            background-size: 20px 20px;
            font-family: 'Poppins', sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; color: #e0e0e0;
        }
        .container {
            background: #121212; width: 100%; max-width: 400px; padding: 40px; 
            border-radius: 8px; border: 1px solid #333;
            box-shadow: 0 0 20px rgba(179, 0, 0, 0.2); position: relative; overflow: hidden;
        }
        .container::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, #8a0000, #ff0000, #8a0000);
        }
        h2 { text-align: center; color: #fff; font-family: 'Courier Prime', monospace; }
        h2 i { color: #b30000; margin-right: 10px; }
        
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 15px; top: 14px; color: #b30000; }
        .input-group input {
            width: 100%; padding: 12px 15px 12px 45px; background: #1e1e1e; border: 1px solid #333;
            color: white; border-radius: 5px; outline: none; transition: 0.3s;
        }
        .input-group input:focus { border-color: #b30000; }

        .btn-submit {
            width: 100%; padding: 12px; background: #b30000; color: white;
            border: none; border-radius: 5px; font-weight: 600; cursor: pointer;
            text-transform: uppercase; transition: 0.3s;
        }
        .btn-submit:hover { background: #ff0000; box-shadow: 0 0 15px rgba(255, 0, 0, 0.4); }

        .links { text-align: center; margin-top: 20px; font-size: 13px; }
        .links a { color: #b30000; text-decoration: none; }
        .links a:hover { text-decoration: underline; color: #ff3333; }

        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; text-align: center; word-break: break-all; }
        .alert-error { background: rgba(183, 28, 28, 0.2); color: #ff8a80; border: 1px solid #ef9a9a; }
        .alert-success { background: rgba(27, 94, 32, 0.2); color: #a5d6a7; border: 1px solid #a5d6a7; }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-unlock-keyhole"></i> RECUPERAR</h2>
        <p style="text-align:center; font-size:13px; color:#888; margin-bottom:20px;">Ingresa tu correo para restablecer el acceso.</p>
        <?php echo $msg; ?>

        <form action="" method="post">
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo registrado" required>
            </div>
            <button type="submit" name="submit" class="btn-submit">Enviar Enlace</button>
        </form>

        <div class="links">
            <a href="login.php">VOLVER AL LOGIN</a>
        </div>
    </div>
    <?php include 'chat_widget.php'; ?>
</body>
</html>
