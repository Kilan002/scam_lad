function toggleChat() {
    const chatWindow = document.getElementById('chatbot-window');
    chatWindow.style.display = chatWindow.style.display === 'block' ? 'none' : 'block';
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const messageText = input.value.trim();
    if (!messageText) return;

    // 1. Mostrar mensaje del usuario
    displayMessage(messageText, 'user');
    input.value = '';

    // 2. Mostrar indicador de carga de la IA
    const loadingMessage = displayMessage('Escribiendo...', 'ai loading');
    
    try {
        // 3. Enviar la pregunta al Backend (chat_process.php)
        const response = await fetch('chat_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt: messageText })
        });

        if (!response.ok) {
            throw new Error('Error en la comunicación con el servidor.');
        }

        const data = await response.json();
        
        // 4. Actualizar con la respuesta de la IA
        updateMessage(loadingMessage, data.response, 'ai');

    } catch (error) {
        console.error('Error del Chatbot:', error);
        updateMessage(loadingMessage, 'Lo siento, ha ocurrido un error al comunicarme con la IA.', 'ai error');
    }
}

// Funciones auxiliares para la interfaz
function displayMessage(text, type) {
    const container = document.getElementById('messages-container');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = text;
    container.appendChild(messageDiv);
    container.scrollTop = container.scrollHeight; // Desplazamiento automático
    return messageDiv;
}

function updateMessage(element, text, newType) {
    element.className = `message ${newType}`;
    element.textContent = text;
    document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;
}