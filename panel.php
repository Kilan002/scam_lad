<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Scam LAD</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .welcome-container {
            max-width: 900px;
            margin: 60px auto;
            background: #121212;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(192, 0, 0, 0.4);
            border: 2px solid #c00000;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #8a0000, #ff0000, #8a0000);
        }

        .welcome-container h2 {
            font-size: 2.5em;
            color: #c00000;
            margin-bottom: 20px;
            text-shadow: 0 0 15px rgba(192, 0, 0, 0.6);
        }

        .welcome-container p {
            font-size: 1.2em;
            color: #d0d0d0;
            margin-bottom: 30px;
        }

        .user-icon {
            font-size: 5em;
            color: #c00000;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(192, 0, 0, 0.5);
        }

        .panel-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .panel-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #c00000 0%, #8a0000 100%);
            color: white;
            border: 2px solid #ff3333;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .panel-btn:hover {
            background: linear-gradient(135deg, #ff0000 0%, #c00000 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.5);
        }

        .panel-btn.secondary {
            background: transparent;
            color: #ff3333;
            border: 2px solid #c00000;
        }

        .panel-btn.secondary:hover {
            background: rgba(192, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .welcome-container {
                padding: 30px 20px;
            }

            .welcome-container h2 {
                font-size: 1.8em;
            }

            .panel-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <h1><i class="fa-solid fa-shield-halved"></i> Scam LAD</h1>
    </div>
    <p>Panel de Control</p>
</header>

<div class="welcome-container">
    <i class="fa-solid fa-user-shield user-icon"></i>
    <h2>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h2>
    <p>Has iniciado sesión correctamente. Bienvenido a tu panel de control seguro.</p>

    <div class="panel-actions">
        <a href="index.html" class="panel-btn secondary">
            <i class="fa-solid fa-home"></i> Ir al Inicio
        </a>
        <a href="about.html" class="panel-btn secondary">
            <i class="fa-solid fa-info-circle"></i> Acerca de
        </a>
        <a href="logout.php" class="panel-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
    </div>
</div>

<footer>
    <p>© 2025 Scam LAD - Sistema de Seguridad Inteligente</p>
</footer>

</body>
</html>