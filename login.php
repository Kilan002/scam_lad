<?php
// --- LÓGICA PHP ---
session_start();

// Si ya está logueado, redirigir
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: home.php");
    die();
}

include 'config.php';
$msg = "";

// CONFIGURACIÓN DE BLOQUEO
define('MAX_INTENTOS', 3);           // Máximo de intentos fallidos
define('TIEMPO_BLOQUEO', 60);        // Tiempo de bloqueo en segundos (60 = 1 minuto)

// Inicializar variables de sesión para intentos
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
    $_SESSION['blocked_until'] = 0;
}

// Verificar si hay mensajes de error por URL
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'timeout') {
        $minutos = isset($_GET['time']) ? $_GET['time'] : 'varios';
        $msg = "<div class='alert alert-error'>⏱️ Tu sesión expiró por inactividad ({$minutos} minutos). Por favor, inicia sesión de nuevo.</div>";
    } elseif ($_GET['error'] === 'no_session') {
        $msg = "<div class='alert alert-error'>⚠️ Debes iniciar sesión para acceder a esta página.</div>";
    }
}

// Procesar el formulario de login
if (isset($_POST['submit'])) {
    
    // --- VERIFICAR SI ESTÁ BLOQUEADO ---
    $tiempo_actual = time();
    
    if ($tiempo_actual < $_SESSION['blocked_until']) {
        $tiempo_restante = $_SESSION['blocked_until'] - $tiempo_actual;
        $msg = "<div class='alert alert-error'>
                    🔒 <strong>CUENTA BLOQUEADA</strong><br>
                    Demasiados intentos fallidos.<br>
                    Intenta de nuevo en <strong>{$tiempo_restante} segundos</strong>.
                </div>";
    } else {
        // El bloqueo ha expirado, resetear intentos
        if ($tiempo_actual >= $_SESSION['blocked_until'] && $_SESSION['blocked_until'] > 0) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['blocked_until'] = 0;
        }
        
        // --- VALIDAR reCAPTCHA ---
        $recaptcha_secret = "6LdMkiwsAAAAAODfCetGtq_cJGnRjlfJIWnE8jJs";
        $recaptcha_response = $_POST['g-recaptcha-response'];
        
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        $response = file_get_contents($verify_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
        $response_data = json_decode($response);
        
        if (!$response_data->success) {
            $msg = "<div class='alert alert-error'>Por favor, completa el reCAPTCHA.</div>";
        } else {
            // reCAPTCHA válido, continuar con login
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, md5($_POST['password']));

            $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) === 1) {
                // --- LOGIN EXITOSO ---
                $row = mysqli_fetch_assoc($result);
                $_SESSION['SESSION_EMAIL'] = $email;
                $_SESSION['SESSION_NAME'] = $row['name'];
                $_SESSION['LAST_ACTIVITY'] = time(); // Timestamp de última actividad
                $_SESSION['CREATED'] = time();
                
                // Resetear intentos fallidos
                $_SESSION['login_attempts'] = 0;
                $_SESSION['blocked_until'] = 0;
                
                header("Location: home.php");
                exit();
            } else {
                // --- LOGIN FALLIDO ---
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = $tiempo_actual;
                
                $intentos_restantes = MAX_INTENTOS - $_SESSION['login_attempts'];
                
                if ($_SESSION['login_attempts'] >= MAX_INTENTOS) {
                    // BLOQUEAR CUENTA
                    $_SESSION['blocked_until'] = $tiempo_actual + TIEMPO_BLOQUEO;
                    $msg = "<div class='alert alert-error'>
                                🔒 <strong>CUENTA BLOQUEADA</strong><br>
                                Has excedido el número máximo de intentos ({MAX_INTENTOS}).<br>
                                Tu cuenta está bloqueada por <strong>" . TIEMPO_BLOQUEO . " segundos</strong>.
                            </div>";
                } else {
                    // Mostrar intentos restantes
                    $msg = "<div class='alert alert-error'>
                                ❌ Credenciales incorrectas.<br>
                                Te quedan <strong>{$intentos_restantes}</strong> intentos antes del bloqueo.
                            </div>";
                }
            }
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
        .btn-submit:disabled {
            background: #555;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-back {
            width: 100%; padding: 10px; margin-top: 12px;
            background: transparent; color: #b30000; border: 1px solid #b30000;
            border-radius: 5px; cursor: pointer; font-weight: 600;
            text-transform: none; letter-spacing: 0.5px;
        }
        .btn-back:hover { background: rgba(179,0,0,0.06); }
        .links { text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .links a { color: #b30000; text-decoration: none; font-weight: bold; }
        .links a:hover { text-decoration: underline; color: #ff3333; }

        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; text-align: center; line-height: 1.6; }
        .alert-error { background: rgba(183, 28, 28, 0.2); color: #ff8a80; border: 1px solid #ef9a9a; }

        .security-info {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid #ff8800;
            color: #ffaa00;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fa-solid fa-fingerprint"></i> ACCESO</h2>
        
        <div class="security-info">
            <i class="fa-solid fa-shield-halved"></i> 
            <strong>Seguridad:</strong> 3 intentos máx. | Auto-logout: 5 min
        </div>

        <?php echo $msg; ?>

        <form action="" method="post">
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo de usuario" required 
                    <?php echo ($tiempo_actual < $_SESSION['blocked_until']) ? 'disabled' : ''; ?>>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" placeholder="Clave de acceso" required
                    <?php echo ($tiempo_actual < $_SESSION['blocked_until']) ? 'disabled' : ''; ?>>
            </div>
            
            <!-- reCAPTCHA Widget -->
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="6LdMkiwsAAAAAARg905G_L9oVkUJy0h2mEL2Y-sy"></div>
            </div>
            
            <button type="submit" name="submit" class="btn-submit" 
                <?php echo ($tiempo_actual < $_SESSION['blocked_until']) ? 'disabled' : ''; ?>>
                <?php 
                if ($tiempo_actual < $_SESSION['blocked_until']) {
                    echo "🔒 BLOQUEADO";
                } else {
                    echo "Entrar al Sistema";
                }
                ?>
            </button>
        </form>

        <div class="links">
            <a href="register.php">CREAR CUENTA NUEVA</a>
            <br><br>
            <a href="forgot-password.php" style="color: #666; font-weight: normal;">Olvidé mi contraseña</a>
        </div>
        <button type="button" class="btn-back" onclick="location.href='index.html'">← Volver al Inicio</button>
    </div>

    <!-- CHATBOT -->
    <?php include 'chat_widget.php'; ?>

    <?php if ($tiempo_actual < $_SESSION['blocked_until']): ?>
    <script>
        // Contador regresivo para el bloqueo
        let tiempoRestante = <?php echo $_SESSION['blocked_until'] - $tiempo_actual; ?>;
        
        const interval = setInterval(() => {
            tiempoRestante--;
            
            if (tiempoRestante <= 0) {
                clearInterval(interval);
                location.reload(); // Recargar página cuando expire el bloqueo
            }
        }, 1000);
    </script>
    <?php endif; ?>

</body>
</html>