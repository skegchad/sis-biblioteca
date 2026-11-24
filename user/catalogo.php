
<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");
include ("../layout/admin/login.php");
include ("../layout/admin/datos_usuario.php");

if ($cargo == "Administrador") {
    $msj = "Ir a página de administrador";
    $rutaAdmin = $URL . "/admin";
} else {
    $msj = "Cerrar Sesión";
    $rutaAdmin = $URL . "/login/controller_logout.php";
}

include ("../layout/user/part1.php");
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= $URL ?>/user/assets/css/estanteria.css">
<link rel="stylesheet" href="<?= $URL ?>/user/assets/css/estanteria-madera.css">
<style>
    a.btn-mis-libros,
a.btn-mis-libros:link,
a.btn-mis-libros:visited,
a.btn-mis-libros:hover,
a.btn-mis-libros:focus,
a.btn-mis-libros:active {
    color: #2b2b2b !important;
    text-decoration: none !important;
    font-weight: 400 !important;
}

a.btn-mis-libros {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: stretch;
    padding: 12px 20px;
    border-radius: 999px;
    border: 1px solid #d9d0b8;
    background-color: #f5f2ea;
    font-size: 0.85rem;
    line-height: 1.2;
    white-space: nowrap;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s, transform 0.1s;
}

a.btn-mis-libros:hover {
    background-color: #ebe5d3;
    border-color: #b8a77a;
}

a.btn-mis-libros:active {
    transform: scale(0.97);
}
/* ===== Agregar a lista / estado de lectura ===== */
.la-caja { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; width: 100%; }
.la-btn {
    padding: 9px 14px; border-radius: 999px; border: 1px solid #d9d0b8;
    background: #f5f2ea; color: #2b2b2b; font-size: .82rem; cursor: pointer;
    box-sizing: border-box; transition: background .2s, border-color .2s;
}
.la-btn:hover { background: #ebe5d3; }
.la-btn.azul { background: #4A90E2; border-color: #4A90E2; color: #fff; }
.la-btn.activo { background: #4A90E2; border-color: #4A90E2; color: #fff; }
.la-estados { display: flex; gap: 6px; flex-wrap: wrap; }
.la-estados .la-btn { flex: 1 1 auto; padding: 8px 10px; font-size: .76rem; }
.la-fav.activo { background: #e0457b; border-color: #e0457b; }

.la-fondo {
    position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none;
    align-items: center; justify-content: center; padding: 16px; z-index: 100000;
}
.la-fondo.ver { display: flex; }
.la-panel {
    background: #f5f2ea; border: 1px solid #d9d0b8; border-radius: 16px; padding: 20px;
    width: 100%; max-width: 380px; max-height: 80vh; overflow-y: auto; box-sizing: border-box;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}
.la-panel h3 { text-align: center; font-size: 1.05rem; margin: 0 0 12px; }
.la-item {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: #fff;
    border-radius: 12px; margin-bottom: 8px; cursor: pointer; font-size: .9rem;
}
.la-item input { width: 18px; height: 18px; cursor: pointer; }
.la-vacio { text-align: center; color: #6b7280; font-size: .85rem; margin: 10px 0; }
.la-nueva { display: flex; gap: 8px; margin-top: 12px; }
.la-nueva input {
    flex: 1; min-width: 0; padding: 9px 14px; border-radius: 999px;
    border: 1px solid #d9d0b8; background: #fff; font-size: .85rem; box-sizing: border-box;
}
.la-cerrar { display: block; margin: 14px auto 0; }
.la-toast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
    background: #1f2937; color: #fff; padding: 10px 20px; border-radius: 999px;
    font-size: .85rem; opacity: 0; pointer-events: none; transition: opacity .25s; z-index: 100001;
}
.la-toast.ver { opacity: 1; }
</style>
<div class="estanteria-app">

    <!-- ============================================================
         CABECERA DEL CATÁLOGO
         ============================================================ -->

    <div class="titulo-seccion">
        <h2>Catálogo de Libros</h2>
    </div>


<!-- ============================================================
     FILTROS Y BÚSQUEDA
     ============================================================ -->

<div class="controles-catalogo">

    <div class="busqueda-catalogo">
<a href="mis_libros.php" class="btn-mis-libros">Mis libros</a>
        <input
            type="text"
            id="f-buscar"
            placeholder="Buscar libros..."
        >

        <button
            type="button"
            id="boton-filtros"
        >
            Filtros
        </button>

    </div>

</div>


<!-- ============================================================
     PANEL DE FILTROS
     ============================================================ -->

<div
    id="panel-filtros"
    class="panel-filtros"
>

    <button
        type="button"
        id="cerrar-filtros"
    >
        ×
    </button>

    <h3>Filtros</h3>


    <label for="f-categoria">
        Categoría
    </label>

    <select id="f-categoria">
        <option value="">
            Todas las categorías
        </option>
    </select>


    <label for="f-subcategoria">
        Subcategoría
    </label>

    <select
        id="f-subcategoria"
        disabled
    >
        <option value="">
            Todas las subcategorías
        </option>
    </select>


    <label for="f-tipo">
        Tipo
    </label>

    <select id="f-tipo">
        <option value="">
            Todos los tipos
        </option>
    </select>


    <label for="f-idioma">
        Idioma
    </label>

    <select id="f-idioma">
        <option value="">
            Todos los idiomas
        </option>
    </select>


    <label for="f-disponibilidad">
        Disponibilidad
    </label>

    <select id="f-disponibilidad">

        <option value="">
            Todos
        </option>

        <option value="1">
            Disponible
        </option>

        <option value="0">
            No disponible
        </option>

    </select>


    <!-- TEMAS -->

    <div class="filtro-temas">

        <button
            type="button"
            id="abrir-temas"
        >
            Seleccionar temas
        </button>

        <div
            id="temas-seleccionados"
            class="temas-seleccionados"
        >
            Ningún tema seleccionado
        </div>

    </div>


    <button
        type="button"
        id="limpiar-filtros"
    >
        Limpiar filtros
    </button>

</div>


<!-- ============================================================
     PANEL DE TEMAS
     ============================================================ -->

<div
    id="panel-temas"
    class="panel-temas"
>

    <button
        type="button"
        id="cerrar-temas"
    >
        ×
    </button>

    <h3>Temas</h3>

    <input
        type="text"
        id="buscar-tema"
        placeholder="Buscar tema..."
    >

    <div
        id="lista-temas"
        class="lista-temas"
    ></div>

    <button
        type="button"
        id="aceptar-temas"
    >
        Aceptar
    </button>

</div>


    <!-- ============================================================
         ESTANTES POR CATEGORÍA — scroll vertical
         El div de abajo crece según cuántas categorías haya (JS le pone
         la altura). El contenido de adentro queda "pegado" (sticky) a la
         pantalla mientras se scrollea, y la cámara 3D avanza por las
         categorías en sincronía con ese scroll.
         ============================================================ -->

    <div id="tabs-categorias" class="tabs-categorias"></div>

    <main id="estantes-categorias" class="estantes-categorias">
        <!-- Acá el JS crea dinámicamente:
            <section class="estante-categoria">
            <h3 class="titulo-estante">Nombre (n)</h3>
            <div class="lienzo-estante">...</div>
            </section>
            una por cada categoría -->
    </main>

    <div id="cargando-estantes" class="cargando-catalogo">
        <div class="spinner"></div>
        <span>Cargando catálogo...</span>
    </div>


    <!-- ============================================================
         VISUALIZACIÓN DE RESULTADOS DE BÚSQUEDA
         (por ahora permanece oculto)
         ============================================================ -->

    <section
        id="vista-busqueda"
        class="vista-busqueda"
        style="display:none;"
    >

        <div class="titulo-seccion">
            <h2>Resultados</h2>
        </div>

        <div
            id="estante-resultados"
            class="estante-wrap"
        >
        </div>

    </section>


    <!-- ============================================================
         INFORMACIÓN DEL LIBRO
         ============================================================ -->

    


    <!-- ============================================================
         SIN RESULTADOS
         ============================================================ -->

    <p
        id="sin-resultados"
        class="sin-resultados"
        style="display:none;"
    >
        No se encontraron libros.
    </p>

    <!-- ============================================================
     INFORMACIÓN DEL LIBRO
     ============================================================ -->

    <div id="info-libro" class="info-libro">
        <div class="botones-lectura">
            <a
                id="boton-leer-pdf"
                class="boton-leer-libro"
                href="#"
                target="_blank"
                rel="noopener"
            >
                <span>LEER PDF</span>
            </a>

            <button
                type="button"
                id="boton-leer-animado"
                class="boton-leer-libro"
            >
                <span>LEER</span>
            </button>
        </div>
        <div class="info-libro-contenido">
  <div class="info-portada-container">
    <img id="info-portada" src="" alt="Portada del libro">
    <div id="lista-acciones"></div>
</div>
            <div class="info-datos">
                <h2 id="info-titulo"></h2>
                <p class="info-autor" id="info-autor"></p>
                <div class="info-descripcion-container"><h3>Descripción</h3><p id="info-descripcion"></p></div>
                <div class="info-metadatos">
                    <div><span>Páginas</span><strong id="info-paginas">—</strong></div>
                    <div><span>Categoría</span><strong id="info-categoria">—</strong></div>
                    <div><span>Tema</span><strong id="info-tema">—</strong></div>
                    <div><span>Tipo</span><strong id="info-tipo">—</strong></div>
                    <div><span>Idioma</span><strong id="info-idioma">—</strong></div>
                    <div><span>Edición</span><strong id="info-edicion">—</strong></div>
                    <div><span>Ejemplares</span><strong id="info-ejemplares">—</strong></div>
                    <div><span>Prestados</span><strong id="info-prestados">—</strong></div>
                    <div><span>Disponibilidad</span><strong id="info-disponibilidad">—</strong></div>
                </div>
            </div>
        </div>
    </div>

        <!-- ============================================================
        LECTOR DE PDF
        ============================================================ -->
    <div id="vuelo-3d-contenedor" class="vuelo-3d-contenedor"></div>
    <div id="lector-pdf" class="lector-pdf">
        <button type="button" id="cerrar-lector" class="cerrar-lector" aria-label="Cerrar lector">×</button>

        <div class="lector-controles-vista">
            <button type="button" id="ver-una-pagina" class="toggle-vista">1 página</button>
            <button type="button" id="ver-dos-paginas" class="toggle-vista activo">2 páginas</button>
        </div>
        <div id="cargando-libro" class="cargando-libro">
            <div class="spinner"></div>
            <span>Cargando libro...</span>
        </div>
        <div id="libro-abierto" class="libro-abierto">
            <div id="paginas-interior" class="paginas-interior">
                <div class="hoja hoja-izq">
                    <canvas id="canvas-pagina-izq"></canvas>
                </div>
                <div class="hoja hoja-der">
                    <canvas id="canvas-pagina-der"></canvas>
                </div>
            </div>
        </div>

            <!-- Portada: tapa el interior al inicio, se abre girando -->
        <div id="portada-flip" class="portada-flip">
            <div class="portada-flip-cara">
                <img id="portada-flip-img" class="portada-flip-img" src="" alt="" style="display:none;">
                <div id="portada-flip-fallback" class="portada-flip-fallback" style="display:none;">
                    <div id="portada-flip-titulo" class="portada-flip-titulo"></div>
                    <div id="portada-flip-autor" class="portada-flip-autor"></div>
                </div>
            </div>
        </div>

        <div class="lector-navegacion">
            <button type="button" id="pagina-anterior" class="nav-pagina" aria-label="Páginas anteriores">◀</button>
            <span id="lector-indicador" class="lector-indicador">1 / 1</span>
            <button type="button" id="pagina-siguiente" class="nav-pagina" aria-label="Páginas siguientes">▶</button>
        </div>
    </div>

</div> <!-- ← ESTE cierra <div class="estanteria-app"> del principio del archivo -->

<script>
    window.APP_URL = "<?= $URL ?>";
    window.PUEDE_PROBAR_RENDIMIENTO = <?= json_encode(
        ($cargo ?? '') === 'Administrador' || in_array(strtolower($_SERVER['HTTP_HOST'] ?? ''), ['localhost', '127.0.0.1'], true)
    ) ?>;
</script>

<!-- ================================================================
     THREE.JS
     ================================================================ -->

<script type="importmap">
{
    "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.165.0/build/three.module.js"
    }
}
</script>
<script>
const API_LISTAS = 'listas/api.php';
const ESTADOS = [
    { key: 'leyendo',     label: 'Leyendo' },
    { key: 'quiero_leer', label: 'Quiero leer' },
    { key: 'leido',       label: 'Ya leído' }
];

function apiL(d) {
    return fetch(API_LISTAS, { method: 'POST', body: new URLSearchParams(d) }).then(r => r.json());
}
function laEl(tag, clase, texto) {
    const e = document.createElement(tag);
    if (clase) e.className = clase;
    if (texto !== undefined) e.textContent = texto;
    return e;
}
function laAviso(txt) {
    let t = document.getElementById('laToast');
    if (!t) { t = laEl('div', 'la-toast'); t.id = 'laToast'; document.body.appendChild(t); }
    t.textContent = txt; t.classList.add('ver');
    setTimeout(() => t.classList.remove('ver'), 2200);
}

function cargarAccionesLista(libroId, contenedor) {
    contenedor = contenedor || document.getElementById('lista-acciones');
    if (!contenedor) return;
    contenedor.innerHTML = '';

    apiL({ accion: 'listas_de_libro', libro_id: libroId }).then(res => {
        if (!res.ok) { contenedor.appendChild(laEl('p', 'la-vacio', res.mensaje || 'No disponible')); return; }
        let estado = res.estado, favorito = res.favorito, listas = res.listas;

        const caja = laEl('div', 'la-caja');

        const bAgregar = laEl('button', 'la-btn azul', 'Agregar a lista');
        bAgregar.onclick = () => abrirPanelListas(libroId, listas, () => cargarAccionesLista(libroId, contenedor));
        caja.appendChild(bAgregar);

        const fila = laEl('div', 'la-estados');
        ESTADOS.forEach(e => {
            const b = laEl('button', 'la-btn' + (estado === e.key ? ' activo' : ''), e.label);
            b.onclick = () => {
                const nuevo = (estado === e.key) ? 'ninguno' : e.key;
                apiL({ accion: 'cambiar_estado', libro_id: libroId, estado: nuevo }).then(r => {
                    laAviso(r.ok ? (nuevo === 'ninguno' ? 'Estado quitado' : 'Marcado como ' + e.label) : r.mensaje);
                    if (r.ok) cargarAccionesLista(libroId, contenedor);
                });
            };
            fila.appendChild(b);
        });
        caja.appendChild(fila);

        const bFav = laEl('button', 'la-btn la-fav' + (favorito ? ' activo' : ''), favorito ? '♥ En favoritos' : '♡ Favorito');
        bFav.onclick = () => {
            apiL({ accion: 'toggle_favorito', libro_id: libroId }).then(r => {
                laAviso(r.mensaje);
                if (r.ok) cargarAccionesLista(libroId, contenedor);
            });
        };
        caja.appendChild(bFav);

        contenedor.appendChild(caja);
    });
}

function abrirPanelListas(libroId, listas, alCerrar) {
    const fondo = laEl('div', 'la-fondo ver');
    const panel = laEl('div', 'la-panel');
    panel.appendChild(laEl('h3', '', 'Agregar a lista'));

    const cuerpo = laEl('div');
    panel.appendChild(cuerpo);

    function pintar(lista) {
        cuerpo.innerHTML = '';
        const propias = lista.filter(l => l.tipo === 'personalizada');
        if (!propias.length) cuerpo.appendChild(laEl('p', 'la-vacio', 'Aún no tienes listas. Crea la primera abajo.'));
        propias.forEach(l => {
            const fila = laEl('label', 'la-item');
            const chk = document.createElement('input');
            chk.type = 'checkbox'; chk.checked = l.contiene;
            chk.onchange = () => {
                apiL({ accion: chk.checked ? 'agregar_a_lista' : 'quitar_de_lista', lista_id: l.id, libro_id: libroId })
                    .then(r => { laAviso(r.mensaje); if (!r.ok) chk.checked = !chk.checked; else l.contiene = chk.checked; });
            };
            fila.appendChild(chk);
            fila.appendChild(laEl('span', '', l.nombre));
            cuerpo.appendChild(fila);
        });
    }
    pintar(listas);

    const nueva = laEl('div', 'la-nueva');
    const inp = document.createElement('input');
    inp.type = 'text'; inp.maxLength = 40; inp.placeholder = 'Nueva lista...';
    const bCrear = laEl('button', 'la-btn azul', '+ Crear');
    bCrear.onclick = () => {
        apiL({ accion: 'crear_lista', nombre: inp.value }).then(r => {
            laAviso(r.mensaje);
            if (!r.ok) return;
            apiL({ accion: 'listas_de_libro', libro_id: libroId }).then(x => {
                if (x.ok) { listas = x.listas; pintar(listas); inp.value = ''; }
            });
        });
    };
    inp.onkeydown = e => { if (e.key === 'Enter') bCrear.click(); };
    nueva.appendChild(inp); nueva.appendChild(bCrear);
    panel.appendChild(nueva);

    const bCerrar = laEl('button', 'la-btn la-cerrar', 'Listo');
    const cerrar = () => { fondo.remove(); if (alCerrar) alCerrar(); };
    bCerrar.onclick = cerrar;
    fondo.onclick = e => { if (e.target === fondo) cerrar(); };
    panel.appendChild(bCerrar);

    fondo.appendChild(panel);
    document.body.appendChild(fondo);
}
</script>
<script
    type="module"
    src="<?= $URL ?>/user/assets/js/estanteria-3d.js"
></script>
<script src="<?= $URL ?>/user/assets/js/estanteria-decoracion.js"></script>

<?php include("../ai/chat_widget.php"); ?>
