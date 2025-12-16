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
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm-password']));
    $code = mysqli_real_escape_string($conn, md5(rand()));

    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
        $msg = "<div class='alert alert-error'>{$email} - Ya existe este correo.</div>";
    } else {
        if ($password === $confirm_password) {
            $sql = "INSERT INTO users (name, email, password, code) VALUES ('{$name}', '{$email}', '{$password}', '{$code}')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $msg = "<div class='alert alert-success'>Registro exitoso. <a href='login.php'>Inicia sesión aquí</a></div>";
            } else {
                $msg = "<div class='alert alert-error'>Algo salió mal. Intenta de nuevo.</div>";
            }
        } else {
            $msg = "<div class='alert alert-error'>Las contraseñas no coinciden.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Scam Lad</title>
    <!-- Fuentes y Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* --- ESTILOS DARK/RED HACKER --- */
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            background-color: #050505; /* Fondo casi negro */
            background-image: radial-gradient(#1a0000 1px, transparent 1px);
            background-size: 20px 20px;
            font-family: 'Poppins', sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; color: #e0e0e0;
        }

        .container {
            background: #121212;
            width: 100%; max-width: 400px;
            padding: 40px; border-radius: 8px;
            border: 1px solid #333;
            box-shadow: 0 0 20px rgba(179, 0, 0, 0.2); /* Resplandor rojo suave */
            position: relative; overflow: hidden;
        }
        
        /* Barra superior roja */
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
        .input-group i {
            position: absolute; left: 15px; top: 14px; color: #b30000;
        }
        .input-group input {
            width: 100%; padding: 12px 15px 12px 45px;
            background: #1e1e1e; border: 1px solid #333;
            color: white; border-radius: 5px; outline: none;
            transition: 0.3s; font-family: 'Poppins', sans-serif;
        }
        .input-group input:focus {
            border-color: #b30000; box-shadow: 0 0 8px rgba(179, 0, 0, 0.4);
        }

        .btn-submit {
            width: 100%; padding: 12px;
            background: #b30000; color: white;
            border: none; border-radius: 5px;
            font-weight: 600; cursor: pointer;
            text-transform: uppercase; letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-submit:hover { background: #ff0000; box-shadow: 0 0 15px rgba(255, 0, 0, 0.4); }

        .links { text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .btn-back {
            width: 100%; padding: 10px; margin-top: 12px;
            background: transparent; color: #b30000; border: 1px solid #b30000;
            border-radius: 5px; cursor: pointer; font-weight: 600;
            text-transform: none; letter-spacing: 0.5px;
        }
        .btn-back:hover { background: rgba(179,0,0,0.06); }
        .links a { color: #b30000; text-decoration: none; font-weight: bold; }
        .links a:hover { text-decoration: underline; color: #ff3333; }

        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; text-align: center; }
        .alert-error { background: rgba(183, 28, 28, 0.2); color: #ff8a80; border: 1px solid #ef9a9a; }
        .alert-success { background: rgba(27, 94, 32, 0.2); color: #a5d6a7; border: 1px solid #a5d6a7; }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fa-solid fa-user-secret"></i> REGISTRO</h2>
        <?php echo $msg; ?>

        <form action="" method="post">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="name" placeholder="Nombre clave / Usuario" value="<?php if (isset($_POST['submit'])) { echo $name; } ?>" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo electrónico" value="<?php if (isset($_POST['submit'])) { echo $email; } ?>" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-check-circle"></i>
                <input type="password" name="confirm-password" placeholder="Confirmar contraseña" required>
            </div>
            <button type="submit" name="submit" class="btn-submit">Crear Cuenta</button>
        </form>

        <div class="links">
            ¿Ya tienes acceso? <a href="login.php">INICIAR SESIÓN</a>
        </div>
        <button type="button" class="btn-back" onclick="location.href='index.php'">← Volver al Inicio</button>
    </div>

    <!-- CHATBOT -->
    <?php include 'chat_widget.php'; ?>

</body>
</html>
