<?php
// chat_widget.php - Widget flotante de chat con BMO
// Requiere que $URL ya esté definido (viene de app/config/config.php)
// Inclúyelo en cualquier página DESPUÉS de incluir config.php, usando el mismo
// número de "../" que usas para llegar a app/config/config.php desde esa página.
?>
<style>
    /* Todo prefijado con .bmo-widget- para no chocar con estilos de otras páginas */
    :root {
      --verde-oscuro:   #1a3d2b;
      --verde-medio:    #2e7d52;
      --verde-claro:    #4caf7d;
      --verde-neon:     #72f5a8;
      --verde-pantalla: #a8ffcc;
      --crema:          #f0fdf4;
    }

    .bmo-widget-btn {
        position: fixed;
        bottom: 4px;
        right: 60px;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background-color: var(--verde-medio);
        color: white;
        border: none;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0,0,0,0.25);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }
    .bmo-widget-btn:hover {
        transform: scale(1.08);
    }
 
    .bmo-widget-panel {
        position: fixed;
        bottom: 80px;
        right: 24px;
        width: 340px;
        max-width: 90vw;
        height: 460px;
        max-height: 75vh;
        background: var(--verde-pantalla);
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        z-index: 9999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        font-family: Arial, sans-serif;
        background-image: "<?php echo $URL; ?>/public/assets/img/grupoProyecto/bmoo.png";
    }
    .bmo-widget-panel.abierto {
        display: flex;
    }
 
    .bmo-widget-header {
        background-color: var(--verde-oscuro);
        color: white;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: bold;
        flex-shrink: 0;
    }
    .bmo-widget-header .bmo-widget-cerrar {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        line-height: 1;
    }
 
    .bmo-widget-mensajes {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        background: var(--verde-pantalla);
        
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
 
    .bmo-widget-msg {
        max-width: 82%;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.4;
        white-space: pre-wrap;
    }
    .bmo-widget-msg.usuario {
        align-self: flex-end;
        background: var(--verde-claro);
        text-align: right;
        color: var(--crema);
    }
    .bmo-widget-msg.bot {
        align-self: flex-start;
        background: var(--verde-medio);
        color: var(--crema);
    }
 
    .bmo-widget-form {
        display: flex;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #e2e2e2;
        flex-shrink: 0;
    }
    .bmo-widget-form input {
        flex: 1;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    .bmo-widget-form button {
        padding: 0 16px;
        border: none;
        background: var(--verde-oscuro);
        color: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    .bmo-widget-form button:disabled {
        opacity: 0.6;
    }
</style>
 
<button class="bmo-widget-btn" id="bmoWidgetBtn" title="Chat con BMO"><img src="<?php echo $URL; ?>/public/assets/img/grupoProyecto/bmoo.png" alt=""></button>
 
<div class="bmo-widget-panel" id="bmoWidgetPanel">
    <div class="bmo-widget-header">
        <span>🤖 Chat con BMO</span>
        <button class="bmo-widget-cerrar" id="bmoWidgetCerrar">✕</button>
    </div>
    <div class="bmo-widget-mensajes" id="bmoWidgetMensajes"></div>
    <form class="bmo-widget-form" id="bmoWidgetForm">
        <input type="text" id="bmoWidgetInput" placeholder="Escríbele a BMO..." autocomplete="off" required>
        <button type="submit" id="bmoWidgetEnviar">Enviar</button>
    </form>
</div>
 
<script>
(function () {
    const URL_CHAT = "<?php echo $URL; ?>/ai/chat_test.php";
 
    const btn = document.getElementById('bmoWidgetBtn');
    const panel = document.getElementById('bmoWidgetPanel');
    const cerrar = document.getElementById('bmoWidgetCerrar');
    const mensajesDiv = document.getElementById('bmoWidgetMensajes');
    const form = document.getElementById('bmoWidgetForm');
    const input = document.getElementById('bmoWidgetInput');
    const enviarBtn = document.getElementById('bmoWidgetEnviar');
 
    let abierto = false;
    let saludoMostrado = false;
 
    function togglePanel() {
        abierto = !abierto;
        panel.classList.toggle('abierto', abierto);
        if (abierto) {
            input.focus();
            if (!saludoMostrado) {
                agregarMensaje('¡Hola! Soy BMO 🤖 ¿En qué puedo ayudarte con la biblioteca?', 'bot');
                saludoMostrado = true;
            }
        }
    }
 
    btn.addEventListener('click', togglePanel);
    cerrar.addEventListener('click', togglePanel);
 
    function agregarMensaje(texto, clase) {
        const div = document.createElement('div');
        div.className = 'bmo-widget-msg ' + clase;
        div.textContent = texto;
        mensajesDiv.appendChild(div);
        mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
    }
 
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const mensaje = input.value.trim();
        if (!mensaje) return;
 
        agregarMensaje(mensaje, 'usuario');
        input.value = '';
        enviarBtn.disabled = true;
        agregarMensaje('Escribiendo...', 'bot');
 
        try {
            const res = await fetch(URL_CHAT, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensaje })
            });
            const data = await res.json();
 
            mensajesDiv.lastChild.remove(); // quitar "Escribiendo..."
            agregarMensaje(data.respuesta, 'bot');
        } catch (err) {
            mensajesDiv.lastChild.remove();
            agregarMensaje('Error al conectar con el servidor.', 'bot');
        } finally {
            enviarBtn.disabled = false;
        }
    });
})();
</script>