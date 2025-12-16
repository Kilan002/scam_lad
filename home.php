<?php
// Incluir gestor de sesiones (verifica inactividad automáticamente)
require_once 'session_manager.php';

// Si llega aquí, el usuario está autenticado y activo
$user_name = $_SESSION['SESSION_NAME'] ?? 'Usuario';
$user_email = $_SESSION['SESSION_EMAIL'] ?? '';

// Calcular tiempo restante de sesión
$tiempo_restante = (TIEMPO_INACTIVIDAD - (time() - $_SESSION['LAST_ACTIVITY']));
$minutos_restantes = floor($tiempo_restante / 60);
$segundos_restantes = $tiempo_restante % 60;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scam LAD - Panel de Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d0000 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid #c00000;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(192, 0, 0, 0.3);
        }

        h1 {
            color: #c00000;
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(192, 0, 0, 0.5);
        }

        p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #e0e0e0;
        }

        /* Indicador de sesión */
        .session-indicator {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid #ff8800;
            border-radius: 8px;
            padding: 15px;
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
        }

        .session-indicator.warning .session-info {
            color: #ff6666;
        }

        .session-timer {
            font-weight: bold;
            font-size: 1.1em;
            color: #ffaa00;
        }

        .session-indicator.warning .session-timer {
            color: #ff0000;
        }

        a {
            color: #ff3333;
            text-decoration: none;
            font-weight: bold;
            padding: 12px 25px;
            border: 2px solid #c00000;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 5px;
        }

        a:hover {
            background-color: #c00000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(192, 0, 0, 0.4);
        }

        .button-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #c00000 0%, #800000 100%);
            color: white !important;
            border: 2px solid #ff3333;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ff0000 0%, #c00000 100%);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: transparent;
            color: #ff3333 !important;
            border: 2px solid #c00000;
        }

        .btn-secondary:hover {
            background: rgba(192, 0, 0, 0.2);
        }

        .logo {
            text-align: center;
            margin: 30px 0;
            font-size: 3em;
            font-weight: bold;
            color: #c00000;
            text-shadow: 0 0 20px rgba(192, 0, 0, 0.8);
            letter-spacing: 5px;
        }

        /* Estilos del Chatbot */
        .chatbot-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #c00000 0%, #800000 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(192, 0, 0, 0.5);
            z-index: 1000;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid #ff3333;
        }

        .chatbot-button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(192, 0, 0, 0.7);
        }

        .chatbot-window {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 380px;
            height: 500px;
            background: #1a1a1a;
            border: 2px solid #c00000;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(192, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #c00000 0%, #800000 100%);
            padding: 15px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #ff3333;
        }

        .messages-container {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background: #0d0d0d;
        }

        .messages-container::-webkit-scrollbar {
            width: 8px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #c00000;
            border-radius: 4px;
        }

        .message {
            padding: 10px 15px;
            margin-bottom: 12px;
            border-radius: 15px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .user {
            background: linear-gradient(135deg, #c00000 0%, #800000 100%);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
            box-shadow: 0 2px 8px rgba(192, 0, 0, 0.3);
        }

        .ai {
            background: #2a2a2a;
            color: #e0e0e0;
            margin-right: auto;
            border-bottom-left-radius: 5px;
            border: 1px solid #3a3a3a;
        }

        .ai.loading {
            font-style: italic;
            opacity: 0.7;
            color: #c00000;
        }

        .ai.error {
            background: #4a0000;
            border: 1px solid #c00000;
        }

        .input-area {
            display: flex;
            padding: 15px;
            background: #1a1a1a;
            border-top: 2px solid #c00000;
        }

        #chat-input {
            flex-grow: 1;
            padding: 12px;
            border: 2px solid #3a3a3a;
            border-radius: 8px;
            margin-right: 10px;
            background: #0d0d0d;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        #chat-input:focus {
            outline: none;
            border-color: #c00000;
            box-shadow: 0 0 10px rgba(192, 0, 0, 0.3);
        }

        .input-area button {
            background: linear-gradient(135deg, #c00000 0%, #800000 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .input-area button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(192, 0, 0, 0.5);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 1.8em;
            }

            .logo {
                font-size: 2em;
            }

            .chatbot-window {
                width: calc(100% - 40px);
                right: 20px;
                left: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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

        <h1>Bienvenido, <?php echo htmlspecialchars($user_name); ?></h1>
        <p>Este es tu panel de control seguro. Email: <strong><?php echo htmlspecialchars($user_email); ?></strong></p>
        
        <div class="logo">TDM51</div>
        
        <div class="button-group">
            <a href="index.html" class="btn-secondary">🏠 Ir al Inicio</a>
            <a href="logout.php" class="btn-primary">🚪 Cerrar Sesión</a>
        </div>
    </div>

    <div class="chatbot-button" onclick="toggleChat()">💬 Chat Asistente</div>
    
    <div class="chatbot-window" id="chatbot-window">
        <div class="chat-header">ScamBot - Asistente Virtual</div>
        <div class="messages-container" id="messages-container">
            <div class="message ai">Hola, soy ScamBot, el asistente virtual de Scam LAD. ¿Cómo puedo ayudarte hoy?</div>
        </div>
        <div class="input-area">
            <input type="text" id="chat-input" placeholder="Escribe tu pregunta..." onkeypress="if(event.key === 'Enter') sendMessage()">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

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
            // Hacer petición AJAX para actualizar LAST_ACTIVITY en el servidor
            fetch('update_activity.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'}
            }).then(() => {
                tiempoRestante = <?php echo TIEMPO_INACTIVIDAD; ?>;
                document.getElementById('session-indicator').classList.remove('warning');
            });
        }
        
        // Detectar actividad
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, () => {
                // Throttle: solo actualizar cada 30 segundos
                if (!window.lastActivity || Date.now() - window.lastActivity > 30000) {
                    window.lastActivity = Date.now();
                    resetearTimer();
                }
            }, true);
        });

        // --- CHATBOT ---
        function toggleChat() {
            const chatWindow = document.getElementById('chatbot-window');
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
            if (chatWindow.style.display === 'flex') {
                document.getElementById('chat-input').focus();
            }
        }

        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const messageText = input.value.trim();
            if (!messageText) return;

            displayMessage(messageText, 'user');
            input.value = '';

            const loadingMessage = displayMessage('ScamBot está escribiendo...', 'ai loading');

            try {
                const response = await fetch('chat_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: messageText })
                });
                
                const data = await response.json();

                if (response.ok && data.response) {
                    updateMessage(loadingMessage, data.response, 'ai');
                } else {
                    const errorMessage = data.error || 'Lo siento, ha ocurrido un error al comunicarme con la IA.';
                    updateMessage(loadingMessage, errorMessage, 'ai error');
                }
            } catch (error) {
                console.error('Error de red/conexión:', error);
                updateMessage(loadingMessage, 'Error de conexión. Verifica el servidor.', 'ai error');
            }
        }

        function displayMessage(text, type) {
            const container = document.getElementById('messages-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.innerHTML = text.replace(/\n/g, '<br>');
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
            return messageDiv;
        }

        function updateMessage(element, text, newType) {
            element.className = `message ${newType}`;
            element.innerHTML = text.replace(/\n/g, '<br>');
            document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;
        }
    </script>
</body>
</html>