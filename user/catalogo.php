
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
            <div class="info-portada-container"><img id="info-portada" src="" alt="Portada del libro"></div>
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

        <div id="libro-abierto" class="libro-abierto">
            <!-- Interior: páginas del PDF, quedan reveladas al abrir la portada -->
            <div id="paginas-interior" class="paginas-interior">
                <div class="hoja hoja-izq">
                    <canvas id="canvas-pagina-izq"></canvas>
                </div>
                <div class="hoja hoja-der">
                    <canvas id="canvas-pagina-der"></canvas>
                </div>
            </div>

            <!-- Portada: tapa el interior al inicio, se abre girando -->
            <div id="portada-flip" class="portada-flip">
                <div id="lomo-flip" class="lomo-flip"></div>
                <div class="portada-flip-cara">
                    <img id="portada-flip-img" class="portada-flip-img" src="" alt="" style="display:none;">
                    <div id="portada-flip-fallback" class="portada-flip-fallback" style="display:none;">
                        <div id="portada-flip-titulo" class="portada-flip-titulo"></div>
                        <div id="portada-flip-autor" class="portada-flip-autor"></div>
                    </div>
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

<script
    type="module"
    src="<?= $URL ?>/user/assets/js/estanteria-3d.js"
></script>

<?php include("../ai/chat_widget.php"); ?>