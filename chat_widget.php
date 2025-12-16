<?php
// Widget de chat minimal para evitar errores cuando se incluye
// Este archivo es intencionalmente ligero; puedes expandirlo luego.
?>
<style>
/* Integración con comm.js: usar IDs que el script espera */
#chatbot-window { position: fixed; right: 18px; bottom: 18px; z-index: 9999; font-family: Poppins, sans-serif; width: 320px; max-width: calc(100vw - 40px); }
#chatbot-toggle { background: #b30000; color: #fff; border: none; border-radius: 50%; width: 56px; height: 56px; cursor: pointer; box-shadow: 0 6px 18px rgba(0,0,0,0.3); }
#chatbot-window .panel { display: none; height: 380px; background: #0f0f0f; color: #eaeaea; border-radius: 8px; margin-bottom: 10px; border: 1px solid #2b2b2b; overflow: hidden; }
#chatbot-window .header { background: linear-gradient(90deg,#8a0000,#ff0000); padding: 10px; display:flex; align-items:center; justify-content:space-between; }
#messages-container { padding: 12px; font-size: 14px; height: calc(100% - 96px); overflow-y: auto; }
.chat-input-row { padding: 8px; border-top: 1px solid #222; display:flex; gap:8px; }
.chat-input-row input { flex:1; padding:8px 10px; border-radius:6px; border:1px solid #333; background:#111; color:#fff; }
.chat-input-row button { background:#b30000; border:none;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer }
</style>
<div id="chatbot-window">
    <div class="panel">
        <div class="header">
            <div style="display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-robot"></i> <strong>Asistente</strong></div>
            <button style="background:transparent;border:none;color:#fff;cursor:pointer" onclick="toggleChat()">✕</button>
        </div>
        <div id="messages-container">
            <div style="opacity:0.85">Hola, soy el chat de ayuda. Escribe y te responderé.</div>
        </div>
        <div class="chat-input-row">
            <input id="chat-input" placeholder="Escribe un mensaje..." />
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>
    <button id="chatbot-toggle" title="Abrir chat" onclick="toggleChat()">💬</button>
</div>

<!-- Incluir comm.js si existe -->
<script>
if (!document.querySelector('script[src="comm.js"]')) {
    const s = document.createElement('script'); s.src = 'comm.js'; document.head.appendChild(s);
}
</script>
