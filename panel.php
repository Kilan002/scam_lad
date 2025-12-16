<?php
// INCLUIR EL GESTOR DE SESIONES (IMPORTANTE)
require_once 'session_manager.php';

// Si llega aquí, el usuario está autenticado
$user_name = $_SESSION['SESSION_NAME'] ?? 'Usuario';
$user_email = $_SESSION['SESSION_EMAIL'] ?? '';

// Calcular tiempo restante
$tiempo_restante = (TIEMPO_INACTIVIDAD - (time() - $_SESSION['LAST_ACTIVITY']));
$minutos_restantes = floor($tiempo_restante / 60);
$segundos_restantes = $tiempo_restante % 60;
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

        /* Indicador de sesión */
        .session-indicator {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid #ff8800;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .session-indicator.warning {
            background: rgba(255, 0, 0, 0.1);
            border-color: #ff0000;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .session-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffaa00;
            font-size: 0.9em;
        }

        .session-indicator.warning .session-info {
            color: #ff6666;
        }

        .session-timer {
            font-weight: bold;
            font-size: 1em;
            color: #ffaa00;
        }

        .session-indicator.warning .session-timer {
            color: #ff0000;
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
    <!-- Indicador de Sesión -->
    <div class="session-indicator" id="session-indicator">
        <div class="session-info">
            <i class="fa-solid fa-clock"></i>
            <span>Sesión activa - Auto-logout en:</span>
        </div>
        <div class="session-timer" id="session-timer">
            <?php echo $minutos_restantes; ?>:<?php echo str_pad($segundos_restantes, 2, '0', STR_PAD_LEFT); ?>
        </div>
    </div>

    <i class="fa-solid fa-user-shield user-icon"></i>
    <h2>Hola, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
    <p>Has iniciado sesión correctamente. Bienvenido a tu panel de control seguro.</p>
    <p style="font-size: 0.9em; color: #888;">Email: <?php echo htmlspecialchars($user_email); ?></p>

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

<script>
    // --- SISTEMA DE CONTROL DE INACTIVIDAD ---
    let tiempoRestante = <?php echo $tiempo_restante; ?>;
    const TIEMPO_ADVERTENCIA = 60; // Advertir cuando queden 60 segundos
    
    // Actualizar contador cada segundo
    const timerInterval = setInterval(() => {
        tiempoRestante--;
        
        const minutos = Math.floor(tiempoRestante / 60);
        const segundos = tiempoRestante % 60;
        
        document.getElementById('session-timer').textContent = 
            minutos + ':' + String(segundos).padStart(2, '0');
        
        // Advertencia cuando queda poco tiempo
        if (tiempoRestante <= TIEMPO_ADVERTENCIA) {
            document.getElementById('session-indicator').classList.add('warning');
        }
        
        // Tiempo agotado - redirigir
        if (tiempoRestante <= 0) {
            clearInterval(timerInterval);
            window.location.href = 'login.php?error=timeout&time=5';
        }
    }, 1000);
    
    // Resetear timer en cualquier actividad del usuario
    function resetearTimer() {
        fetch('update_activity.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        }).then(() => {
            tiempoRestante = <?php echo TIEMPO_INACTIVIDAD; ?>;
            document.getElementById('session-indicator').classList.remove('warning');
        });
    }
    
    // Detectar actividad (Throttle: cada 30 segundos)
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
        document.addEventListener(event, () => {
            if (!window.lastActivity || Date.now() - window.lastActivity > 30000) {
                window.lastActivity = Date.now();
                resetearTimer();
            }
        }, true);
    });
</script>

</body>
</html>