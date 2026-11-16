<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");
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

<style>
.ml-wrap, .ml-wrap * { box-sizing: border-box; }
.ml-wrap { max-width: 1000px; margin: 24px auto; padding: 0 12px; }
.ml-title { text-align: center; font-size: 2rem; margin-bottom: 6px; }
.ml-back { display: block; text-align: center; margin-bottom: 18px; font-size: .9rem; }

/* Pestañas */
.ml-tabs { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px; }
.ml-tab {
    padding: 10px 18px; border-radius: 999px; border: 1px solid #d9d0b8;
    background: #f5f2ea; color: #2b2b2b; font-size: .85rem; cursor: pointer;
    transition: background .2s, border-color .2s;
}
.ml-tab:hover { background: #ebe5d3; }
.ml-tab.activa { background: #4A90E2; border-color: #4A90E2; color: #fff; }

/* Panel */
.ml-panel { background: #f5f2ea; border: 1px solid #d9d0b8; border-radius: 16px; padding: 18px; min-height: 200px; }
.ml-vacio { text-align: center; color: #6b7280; padding: 30px 10px; font-size: .95rem; }

/* Grilla de libros */
.ml-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 14px; }
.ml-book { background: #fff; border-radius: 12px; padding: 8px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
.ml-book img { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; background: #cbd5e1; display: block; }
.ml-book p { font-size: .8rem; margin: 8px 0 6px; line-height: 1.3; word-break: break-word; }

/* Botones */
.ml-btn {
    padding: 7px 14px; border-radius: 999px; border: 1px solid #d9d0b8;
    background: #f5f2ea; font-size: .78rem; cursor: pointer; color: #2b2b2b;
}
.ml-btn:hover { background: #ebe5d3; }
.ml-btn.peligro { border-color: #e5a3a3; color: #b42318; }
.ml-btn.peligro:hover { background: #fde8e8; }
.ml-btn.azul { background: #4A90E2; border-color: #4A90E2; color: #fff; }

/* Crear lista */
.ml-crear { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 18px; }
.ml-crear input {
    padding: 10px 16px; border-radius: 999px; border: 1px solid #d9d0b8;
    background: #fff; min-width: 220px; font-size: .9rem;
}

/* Tarjeta de lista personalizada */
.ml-lista { background: #fff; border-radius: 14px; padding: 14px; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,.05); }
.ml-lista-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 12px; }
.ml-lista-head h3 { font-size: 1.05rem; }
.ml-lista-head .acciones { display: flex; gap: 8px; }

/* Aviso */
.ml-toast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
    background: #1f2937; color: #fff; padding: 10px 20px; border-radius: 999px;
    font-size: .85rem; opacity: 0; pointer-events: none; transition: opacity .25s; z-index: 9999;
}
.ml-toast.ver { opacity: 1; }

/* ===== Modal propio ===== */
.ml-modal-fondo {
    position: fixed; inset: 0; background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
    padding: 16px; z-index: 10000;
}
.ml-modal-fondo.ver { display: flex; }

.ml-modal {
    background: #f5f2ea; border: 1px solid #d9d0b8; border-radius: 16px;
    padding: 22px; width: 100%; max-width: 400px;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
    animation: mlPop .18s ease-out;
}
@keyframes mlPop {
    from { transform: scale(.94); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.ml-modal h3 { font-size: 1.1rem; margin-bottom: 6px; text-align: center; }
.ml-modal p  { font-size: .9rem; color: #4b5563; text-align: center; margin-bottom: 14px; }
.ml-modal input#mlModalInput {
    display: block;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box !important;
    padding: 11px 16px;
    border-radius: 999px;
    border: 1px solid #d9d0b8;
    background: #fff;
    font-size: .95rem;
    margin: 0 0 16px 0;
    outline: none;
}
.ml-modal input#mlModalInput:focus { border-color: #4A90E2; }
.ml-modal input:focus { border-color: #4A90E2; }
.ml-modal-botones { display: flex; gap: 10px; justify-content: center; }
.ml-btn.rojo { background: #b42318; border-color: #b42318; color: #fff; }
/* ===== Modal propio ===== */
.ml-modal-fondo {
    position: fixed; inset: 0; background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
    padding: 16px; z-index: 10000;
}
.ml-modal-fondo.ver { display: flex; }

.ml-modal {
    background: #f5f2ea; border: 1px solid #d9d0b8; border-radius: 16px;
    padding: 22px; width: 100%; max-width: 400px;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
    animation: mlPop .18s ease-out;
}
@keyframes mlPop {
    from { transform: scale(.94); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.ml-modal h3 { font-size: 1.1rem; margin-bottom: 6px; text-align: center; }
.ml-modal p  { font-size: .9rem; color: #4b5563; text-align: center; margin-bottom: 14px; }
.ml-modal input {
    width: 100%; padding: 11px 16px; border-radius: 999px;
    border: 1px solid #d9d0b8; background: #fff; font-size: .95rem;
    margin-bottom: 16px; outline: none;
}
.ml-modal input:focus { border-color: #4A90E2; }
.ml-modal-botones { display: flex; gap: 10px; justify-content: center; }
.ml-btn.rojo { background: #b42318; border-color: #b42318; color: #fff; }
</style>

<div class="ml-wrap">
    <h1 class="ml-title">Mis libros</h1>
    <a class="ml-back" href="catalogo.php">← Volver al catálogo</a>

    <div class="ml-tabs" id="mlTabs"></div>
    <div class="ml-panel" id="mlPanel">Cargando...</div>
</div>

<div class="ml-toast" id="mlToast"></div>
<div class="ml-modal-fondo" id="mlModalFondo">
    <div class="ml-modal">
        <h3 id="mlModalTitulo"></h3>
        <p id="mlModalTexto"></p>
        <input type="text" id="mlModalInput" maxlength="40">
        <div class="ml-modal-botones">
            <button class="ml-btn" id="mlModalCancelar">Cancelar</button>
            <button class="ml-btn azul" id="mlModalAceptar">Aceptar</button>
        </div>
    </div>
</div>
<div class="ml-modal-fondo" id="mlModalFondo">
    <div class="ml-modal">
        <h3 id="mlModalTitulo"></h3>
        <p id="mlModalTexto"></p>
        <input type="text" id="mlModalInput" maxlength="40">
        <div class="ml-modal-botones">
            <button class="ml-btn" id="mlModalCancelar">Cancelar</button>
            <button class="ml-btn azul" id="mlModalAceptar">Aceptar</button>
        </div>
    </div>
</div>
<script>
// ===== CONFIGURA ESTO =====
const API = 'listas/api.php';
// Carpeta donde están las portadas (ajústala, ver "Pendiente" abajo)
const RUTA_PORTADAS = '../public/uploads/';
// ==========================

const TABS = [
    { key: 'leyendo',       label: 'Leyendo' },
    { key: 'quiero_leer',   label: 'Quiero leer' },
    { key: 'leido',         label: 'Ya leído' },
    { key: 'favoritos',     label: 'Favoritos' },
    { key: 'personalizada', label: 'Mis listas' }
];

let listas = [];
let tabActiva = 'leyendo';

const $tabs  = document.getElementById('mlTabs');
const $panel = document.getElementById('mlPanel');
const $toast = document.getElementById('mlToast');

function api(datos) {
    return fetch(API, { method: 'POST', body: new URLSearchParams(datos) })
        .then(r => r.json());
}

function aviso(texto) {
    $toast.textContent = texto;
    $toast.classList.add('ver');
    setTimeout(() => $toast.classList.remove('ver'), 2200);
}
// ===== Modal propio (reemplaza prompt y confirm) =====
const $mFondo   = document.getElementById('mlModalFondo');
const $mTitulo  = document.getElementById('mlModalTitulo');
const $mTexto   = document.getElementById('mlModalTexto');
const $mInput   = document.getElementById('mlModalInput');
const $mAceptar = document.getElementById('mlModalAceptar');
const $mCancel  = document.getElementById('mlModalCancelar');

function abrirModal({ titulo, texto = '', conInput = false, valor = '', textoBoton = 'Aceptar', peligro = false }) {
    return new Promise(resolve => {
        $mTitulo.textContent = titulo;
        $mTexto.textContent  = texto;
        $mTexto.style.display = texto ? 'block' : 'none';
        $mInput.style.display = conInput ? 'block' : 'none';
        $mInput.value = valor;
        $mAceptar.textContent = textoBoton;
        $mAceptar.className = 'ml-btn ' + (peligro ? 'rojo' : 'azul');
        $mFondo.classList.add('ver');
        if (conInput) { $mInput.focus(); $mInput.select(); }

        function cerrar(resultado) {
            $mFondo.classList.remove('ver');
            $mAceptar.onclick = $mCancel.onclick = $mFondo.onclick = $mInput.onkeydown = null;
            document.onkeydown = null;
            resolve(resultado);
        }
        $mAceptar.onclick = () => cerrar(conInput ? $mInput.value : true);
        $mCancel.onclick  = () => cerrar(null);
        $mFondo.onclick   = e => { if (e.target === $mFondo) cerrar(null); };
        $mInput.onkeydown = e => { if (e.key === 'Enter') $mAceptar.click(); };
        document.onkeydown = e => { if (e.key === 'Escape') cerrar(null); };
    });
}


function abrirModal({ titulo, texto = '', conInput = false, valor = '', textoBoton = 'Aceptar', peligro = false }) {
    return new Promise(resolve => {
        $mTitulo.textContent = titulo;
        $mTexto.textContent  = texto;
        $mTexto.style.display = texto ? 'block' : 'none';
        $mInput.style.display = conInput ? 'block' : 'none';
        $mInput.value = valor;
        $mAceptar.textContent = textoBoton;
        $mAceptar.className = 'ml-btn ' + (peligro ? 'rojo' : 'azul');
        $mFondo.classList.add('ver');
        if (conInput) { $mInput.focus(); $mInput.select(); }

        function cerrar(resultado) {
            $mFondo.classList.remove('ver');
            $mAceptar.onclick = $mCancel.onclick = $mFondo.onclick = $mInput.onkeydown = null;
            document.onkeydown = null;
            resolve(resultado);
        }
        $mAceptar.onclick = () => cerrar(conInput ? $mInput.value : true);
        $mCancel.onclick  = () => cerrar(null);
        $mFondo.onclick   = e => { if (e.target === $mFondo) cerrar(null); };
        $mInput.onkeydown = e => { if (e.key === 'Enter') $mAceptar.click(); };
        document.onkeydown = e => { if (e.key === 'Escape') cerrar(null); };
    });
}

function pedirTexto(titulo, valor) {
    return abrirModal({ titulo, conInput: true, valor, textoBoton: 'Guardar' });
}
function confirmar(titulo, texto) {
    return abrirModal({ titulo, texto, textoBoton: 'Eliminar', peligro: true });
}

function pedirTexto(titulo, valor) {
    return abrirModal({ titulo, conInput: true, valor, textoBoton: 'Guardar' });
}
function confirmar(titulo, texto) {
    return abrirModal({ titulo, texto, textoBoton: 'Eliminar', peligro: true });
}

function urlPortada(p) {
    if (!p) return '';
    if (/^(https?:)?\/\//.test(p) || p.startsWith('/') || p.startsWith('../')) return p;
    return RUTA_PORTADAS + p;
}

function el(tag, clase, texto) {
    const e = document.createElement(tag);
    if (clase) e.className = clase;
    if (texto !== undefined) e.textContent = texto;
    return e;
}

function cargar() {
    api({ accion: 'obtener_listas' }).then(res => {
        if (!res.ok) {
            $panel.innerHTML = '';
            $panel.appendChild(el('p', 'ml-vacio', res.mensaje || 'No se pudieron cargar tus listas'));
            return;
        }
        listas = res.listas;
        render();
    }).catch(() => {
        $panel.innerHTML = '';
        $panel.appendChild(el('p', 'ml-vacio', 'Error de conexión con el servidor'));
    });
}

function tarjetaLibro(libro, listaId) {
    const card = el('div', 'ml-book');
    const img = el('img');
    img.src = urlPortada(libro.portada);
    img.alt = libro.titulo;
    card.appendChild(img);
    card.appendChild(el('p', '', libro.titulo));

    const btn = el('button', 'ml-btn peligro', 'Quitar');
    btn.onclick = () => {
        api({ accion: 'quitar_de_lista', lista_id: listaId, libro_id: libro.id })
            .then(r => { aviso(r.mensaje); if (r.ok) cargar(); });
    };
    card.appendChild(btn);
    return card;
}

function grillaLibros(lista) {
    if (!lista.libros.length) return el('p', 'ml-vacio', 'Aún no hay libros en esta lista.');
    const g = el('div', 'ml-grid');
    lista.libros.forEach(l => g.appendChild(tarjetaLibro(l, lista.id)));
    return g;
}

function renderTabs() {
    $tabs.innerHTML = '';
    TABS.forEach(t => {
        const b = el('button', 'ml-tab' + (t.key === tabActiva ? ' activa' : ''), t.label);
        b.onclick = () => { tabActiva = t.key; render(); };
        $tabs.appendChild(b);
    });
}

function renderPersonalizadas() {
    // Formulario para crear lista
    const form = el('div', 'ml-crear');
    const input = el('input');
    input.type = 'text';
    input.maxLength = 40;
    input.placeholder = 'Nombre de la nueva lista';
    const crear = el('button', 'ml-btn azul', '+ Crear lista');
    crear.onclick = () => {
        api({ accion: 'crear_lista', nombre: input.value }).then(r => {
            aviso(r.mensaje);
            if (r.ok) cargar();
        });
    };
    input.addEventListener('keydown', e => { if (e.key === 'Enter') crear.click(); });
    form.appendChild(input);
    form.appendChild(crear);
    $panel.appendChild(form);

    // Listas personalizadas
    const propias = listas.filter(l => l.tipo === 'personalizada');
    if (!propias.length) {
        $panel.appendChild(el('p', 'ml-vacio', 'Todavía no has creado listas. ¡Crea la primera!'));
        return;
    }
    propias.forEach(lista => {
        const caja = el('div', 'ml-lista');
        const head = el('div', 'ml-lista-head');
        head.appendChild(el('h3', '', lista.nombre + ' (' + lista.libros.length + ')'));

        const acc = el('div', 'acciones');
        const ren = el('button', 'ml-btn', 'Renombrar');
ren.onclick = async () => {
    const nuevo = await pedirTexto('Renombrar lista', lista.nombre);
    if (nuevo === null) return;
    api({ accion: 'renombrar_lista', lista_id: lista.id, nombre: nuevo })
        .then(r => { aviso(r.mensaje); if (r.ok) cargar(); });
};
const del = el('button', 'ml-btn peligro', 'Eliminar');
del.onclick = async () => {
    const ok = await confirmar('¿Eliminar lista?', 'Se borrará "' + lista.nombre + '" y todo lo que tiene dentro.');
    if (!ok) return;
    api({ accion: 'eliminar_lista', lista_id: lista.id })
        .then(r => { aviso(r.mensaje); if (r.ok) cargar(); });
};
        acc.appendChild(ren);
        acc.appendChild(del);
        head.appendChild(acc);

        caja.appendChild(head);
        caja.appendChild(grillaLibros(lista));
        $panel.appendChild(caja);
    });
}

function render() {
    renderTabs();
    $panel.innerHTML = '';

    if (tabActiva === 'personalizada') {
        renderPersonalizadas();
        return;
    }
    const lista = listas.find(l => l.tipo === tabActiva);
    if (!lista) { $panel.appendChild(el('p', 'ml-vacio', 'Lista no encontrada')); return; }
    $panel.appendChild(grillaLibros(lista));
}

cargar();
</script>

<?php include("../ai/chat_widget.php"); ?>