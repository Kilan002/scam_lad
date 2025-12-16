<?php
// Widget de chat minimal para evitar errores cuando se incluye
// Este archivo es intencionalmente ligero; puedes expandirlo luego.
?>
<style>
#chat-widget { position: fixed; right: 18px; bottom: 18px; z-index: 9999; font-family: Poppins, sans-serif; }
#chat-toggle { background: #b30000; color: #fff; border: none; border-radius: 50%; width: 56px; height: 56px; cursor: pointer; box-shadow: 0 6px 18px rgba(0,0,0,0.3); }
#chat-box { display: none; width: 320px; max-width: calc(100vw - 40px); height: 380px; background: #0f0f0f; color: #eaeaea; border-radius: 8px; margin-bottom: 10px; border: 1px solid #2b2b2b; overflow: hidden; }
#chat-header { background: linear-gradient(90deg,#8a0000,#ff0000); padding: 10px; display:flex; align-items:center; justify-content:space-between; }
#chat-body { padding: 12px; font-size: 14px; height: calc(100% - 96px); overflow-y: auto; }
#chat-input { padding: 8px; border-top: 1px solid #222; display:flex; gap:8px; }
#chat-input input { flex:1; padding:8px 10px; border-radius:6px; border:1px solid #333; background:#111; color:#fff; }
#chat-input button { background:#b30000; border:none;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer }
</style>
<div id="chat-widget">
    <div id="chat-box">
        <div id="chat-header">
            <div style="display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-robot"></i> <strong>Asistente</strong></div>
            <button style="background:transparent;border:none;color:#fff;cursor:pointer" onclick="toggleChat()">✕</button>
        </div>
        <div id="chat-body">
            <div style="opacity:0.85">Hola, soy el chat de ayuda. Escribe y te responderé (simulado).</div>
        </div>
        <div id="chat-input">
            <input id="chat-msg" placeholder="Escribe un mensaje..." />
            <button onclick="sendChat()">Enviar</button>
        </div>
    </div>
    <button id="chat-toggle" title="Abrir chat" onclick="toggleChat()">💬</button>
</div>
<script>
function toggleChat(){
    const box = document.getElementById('chat-box');
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}
function sendChat(){
    const input = document.getElementById('chat-msg');
    if(!input.value) return;
    const body = document.getElementById('chat-body');
    const p = document.createElement('div');
    p.style.marginBottom='8px';
    p.innerHTML = '<strong>Tú:</strong> '+escapeHtml(input.value);
    body.appendChild(p);
    input.value='';
    body.scrollTop = body.scrollHeight;
    // Respuesta simulada
    setTimeout(()=>{
        const r = document.createElement('div');
        r.style.opacity='0.9'; r.style.marginBottom='8px';
        r.innerHTML = '<strong>Asistente:</strong> Gracias por tu mensaje. (Respuesta simulada)';
        body.appendChild(r);
        body.scrollTop = body.scrollHeight;
    },600);
}
function escapeHtml(text){ return text.replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }
</script>
