<?php
require '../app/config/config.php';    // define las constantes BD_SISTEMA, BD_SERVIDOR, etc.
require '../app/config/conexion.php';  // usa esas constantes y crea $pdo
include ("../layout/admin/login.php");
include ("../layout/admin/datos_usuario.php");
if($cargo=="Administrador"){
    $msj="Ir a página de administrador";
    $rutaAdmin= $URL."/admin";
}else{
    $msj="Cerrar Sesión";
    $rutaAdmin= $URL."/login/controller_logout.php";
}
include ("../layout/user/part1.php");
?>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    :root {
      --verde-oscuro:   #1a3d2b;
      --verde-medio:    #2e7d52;
      --verde-claro:    #4caf7d;
      --verde-neon:     #72f5a8;
      --verde-pantalla: #a8ffcc;
      --crema:          #f0fdf4;
    }
    #chat {
    border: 1px solid #2e7d52;
    border-radius: 8px;
    padding: 15px;
    height: 400px;
    overflow-y: auto;
    margin-bottom: 10px;

    background-color: #c0f7d7;
    background-image: url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/bmoo.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: 10%;
}
    .msg { margin: 8px 0; padding: 8px 12px; border-radius: 8px; max-width: 80%; }
    .user { background: var(--verde-medio); margin-left: auto; text-align: right; color: #ccc; }
    .bot { background: var(--verde-oscuro); color: #ccc;}
    
    .chat-container input { flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
    .chat-container button { padding: 10px 16px; border: none; background: #2563eb; color: white; border-radius: 6px; cursor: pointer; }
    .chat-container button:disabled { opacity: 0.6; }

    .chat-container form {display: flex; gap: 8px; }
    .chat-container {font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; }
    body{
        background-color: var(--verde-oscuro);
        }
</style>


<div class="chat-container">
<h2 style="color: #f0fdf4">Chat BMO</h2>
<div id="chat"></div>

<form id="form">
    <input type="text" id="mensaje" placeholder="Escribe aquí tu mensaje..." autocomplete="off" required>
    <button type="submit" id="btn">Enviar</button>
</form>
</div>

<script>
const chat = document.getElementById('chat');
const form = document.getElementById('form');
const input = document.getElementById('mensaje');
const btn = document.getElementById('btn');

function agregarMensaje(texto, clase) {

    const div = document.createElement('div');
    div.className = 'msg ' + clase;

    if (clase === 'bot') {
        div.innerHTML = marked.parse(texto);
    } else {
        div.textContent = texto;
    }

    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const mensaje = input.value.trim();
    if (!mensaje) return;

    agregarMensaje(mensaje, 'user');
    input.value = '';
    btn.disabled = true;
    agregarMensaje('Escribiendo...', 'bot');

    try {
        const res = await fetch('chat_test.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mensaje })
        });
        const data = await res.json();

        chat.lastChild.remove(); // quitar "Escribiendo..."
        agregarMensaje(data.respuesta, 'bot');
    } catch (err) {
        chat.lastChild.remove();
        agregarMensaje('Error al conectar con el servidor.', 'bot');
    } finally {
        btn.disabled = false;
    }
});
</script>

<?php
// Cerramos aquí lo que part1.php dejó abierto (<div id="wrapper"> y <header>...</header> ya cerrado,
// solo falta cerrar el wrapper, body y html), ya que no existe un part2.php/footer.php en el proyecto.
?>
</div><!-- cierre de #wrapper -->
</body>
</html>