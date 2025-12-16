<?php
$msg = "";
include 'config.php';

// Verificar si hay código en la URL
if (isset($_GET['reset'])) {
    $reset_code = mysqli_real_escape_string($conn, $_GET['reset']);
    
    // Verificar si el código existe en la BD
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$reset_code}'")) > 0) {
        
        if (isset($_POST['submit'])) {
            $password = mysqli_real_escape_string($conn, md5($_POST['password']));
            $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm-password']));

            if ($password === $confirm_password) {
                // Actualizar contraseña y borrar el código para que no se use dos veces
                $query = mysqli_query($conn, "UPDATE users SET password='{$password}', code='' WHERE code='{$reset_code}'");

                if ($query) {
                    header("Location: login.php");
                }
            } else {
                $msg = "<div class='alert alert-error'>Las contraseñas no coinciden.</div>";
            }
        }
    } else {
        $msg = "<div class='alert alert-error'>El enlace es inválido o ya fue utilizado.</div>";
    }
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - Scam Lad</title>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Mismos estilos Dark -->
    <style>
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
            box-shadow: 0 0 20px rgba(179, 0, 0, 0.2); position: relative;
        }
        .container::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, #8a0000, #ff0000, #8a0000);
        }
        h2 { text-align: center; color: #fff; font-family: 'Courier Prime', monospace; }
        
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
        .btn-submit:hover { background: #ff0000; }

        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; text-align: center; }
        .alert-error { background: rgba(183, 28, 28, 0.2); color: #ff8a80; border: 1px solid #ef9a9a; }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-key"></i> NUEVA CLAVE</h2>
        <?php echo $msg; ?>

        <form action="" method="post">
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Nueva Contraseña" required>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-check-circle"></i>
                <input type="password" name="confirm-password" placeholder="Confirmar Nueva" required>
            </div>
            <button type="submit" name="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
    <?php include 'chat_widget.php'; ?>
</body>
</html>
