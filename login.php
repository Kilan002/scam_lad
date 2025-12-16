<?php
// --- LÓGICA PHP ---
session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: home.php");
    die();
}
include 'config.php';
$msg = "";

if (isset($_POST['submit'])) {
    // VALIDAR reCAPTCHA
    $recaptcha_secret = "6LdMkiwsAAAAAODfCetGtq_cJGnRjlfJIWnE8jJs"; // Reemplaza con tu Secret Key
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    // Verificar reCAPTCHA con Google
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
    $response_data = json_decode($response);
    
    if (!$response_data->success) {
        $msg = "<div class='alert alert-error'>Por favor, completa el reCAPTCHA.</div>";
    } else {
        // Si reCAPTCHA es válido, continuar con el login
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));

        $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['SESSION_EMAIL'] = $email;
            $_SESSION['SESSION_NAME'] = $row['name'];
            header("Location: home.php");
        } else {
            $msg = "<div class='alert alert-error'>Credenciales incorrectas.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Scam Lad</title>
    <!-- Fuentes y Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        /* --- MISMOS ESTILOS QUE REGISTER --- */
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            background-color: #050505;
            background-image: radial-gradient(#1a0000 1px, transparent 1px);
            background-size: 20px 20px;
            font-family: 'Poppins', sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; color: #e0e0e0;
        }
        .container {
            background: #121212; width: 100%; max-width: 380px; padding: 40px;
            border-radius: 8px; border: 1px solid #333;
            box-shadow: 0 0 20px rgba(179, 0, 0, 0.2); position: relative; overflow: hidden;
        }
        .container::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, #8a0000, #ff0000, #8a0000);
        }
        h2 {
            text-align: center; color: #fff; margin-bottom: 30px;
            font-family: 'Courier Prime', monospace; letter-spacing: 1px;
        }
        h2 i { color: #b30000; margin-right: 10px; }

        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 15px; top: 14px; color: #b30000; }
        .input-group input {
            width: 100%; padding: 12px 15px 12px 45px;
            background: #1e1e1e; border: 1px solid #333;
            color: white; border-radius: 5px; outline: none; transition: 0.3s;
        }
        .input-group input:focus { border-color: #b30000; box-shadow: 0 0 8px rgba(179, 0, 0, 0.4); }

        /* Estilo para centrar el reCAPTCHA */
        .recaptcha-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .btn-submit {
            width: 100%; padding: 12px; background: #b30000; color: white;
            border: none; border-radius: 5px; font-weight: 600; cursor: pointer;
            text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
        }
        .btn-submit:hover { background: #ff0000; box-shadow: 0 0 15px rgba(255, 0, 0, 0.4); }

        .links { text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .links a { color: #b30000; text-decoration: none; font-weight: bold; }
        .links a:hover { text-decoration: underline; color: #ff3333; }

        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; text-align: center; }
        .alert-error { background: rgba(183, 28, 28, 0.2); color: #ff8a80; border: 1px solid #ef9a9a; }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fa-solid fa-fingerprint"></i> ACCESO</h2>
        <?php echo $msg; ?>

        <form action="" method="post">
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo de usuario" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" placeholder="Clave de acceso" required>
            </div>
            
            <!-- reCAPTCHA Widget -->
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="6LdMkiwsAAAAAARg905G_L9oVkUJy0h2mEL2Y-sy"></div>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Entrar al Sistema</button>
        </form>

        <div class="links">
            <a href="register.php">CREAR CUENTA NUEVA</a>
            <br><br>
            <a href="forgot-password.php" style="color: #666; font-weight: normal;">Olvidé mi contraseña</a>
        </div>
    </div>

    <!-- CHATBOT -->
    <?php include 'chat_widget.php'; ?>

</body>
</html>
