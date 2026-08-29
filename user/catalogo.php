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
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $URL ?>/user/assets/css/estanteria.css">

<div class="estanteria-app">

  <div class="titulo-seccion">
    <h2>Catálogo de Libros</h2>
  </div>

  <div class="barra-busqueda">

  <button
    id="boton-filtros"
    class="boton-filtros"
    type="button"
  >
    ☰
    <span>Filtros</span>
  </button>

  <div class="busqueda">

    <span class="icono-busqueda">⌕</span>

    <input
      id="f-buscar"
      type="text"
      placeholder="Buscar por título o autor…"
    >

  </div>

    </div>


    <!-- ============================================================
        PANEL PRINCIPAL DE FILTROS
        ============================================================ -->

    <div id="panel-filtros" class="panel-filtros">

      <div class="panel-filtros-cabecera">

        <h3>Filtros</h3>

        <button
          id="cerrar-filtros"
          type="button"
        >
          ×
        </button>

      </div>


      <div class="panel-filtros-contenido">

        <!-- CATEGORÍA -->

        <label for="f-categoria">
          Categoría
        </label>

        <select id="f-categoria">
          <option value="">
            Todas las categorías
          </option>
        </select>


        <!-- SUBCATEGORÍA -->

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


        <!-- TIPO -->

        <label for="f-tipo">
          Tipo
        </label>

        <select id="f-tipo">

          <option value="">
            Todos los tipos
          </option>

        </select>


        <!-- IDIOMA -->

        <label for="f-idioma">
          Idioma
        </label>

        <select id="f-idioma">

          <option value="">
            Todos los idiomas
          </option>

        </select>


        <!-- DISPONIBILIDAD -->

        <label for="f-disponibilidad">
          Disponibilidad
        </label>

        <select id="f-disponibilidad">

          <option value="">
            Toda disponibilidad
          </option>

          <option value="disponible">
            Disponible
          </option>

          <option value="no_disponible">
            No disponible
          </option>

        </select>


        <!-- TEMAS -->

        <div class="filtro-temas-principal">

          <div class="titulo-filtro-temas">

            <span>Temas</span>

            <button
              id="abrir-temas"
              type="button"
            >
              Seleccionar →
            </button>

          </div>


          <div
            id="temas-seleccionados"
            class="temas-seleccionados"
          >
            Ningún tema seleccionado
          </div>

        </div>


        <!-- LIMPIAR -->

        <button
          id="limpiar-filtros"
          class="limpiar-filtros"
          type="button"
        >
          Limpiar filtros
        </button>

      </div>

    </div>


    <!-- ============================================================
        PANEL DE SELECCIÓN DE TEMAS
        ============================================================ -->

    <div id="panel-temas" class="panel-temas">

      <div class="panel-temas-contenido">

        <div class="panel-temas-header">

          <h3>Seleccionar temas</h3>

          <button
            type="button"
            id="cerrar-temas"
          >
            ×
          </button>

        </div>


        <!-- BUSCAR TEMAS -->

        <input
          type="text"
          id="buscar-tema"
          placeholder="Buscar temas..."
        >


        <!-- LISTA -->

        <div
          id="lista-temas"
          class="lista-temas"
        ></div>


        <!-- ACCIONES -->

        <div class="temas-acciones">

          <button
            type="button"
            id="limpiar-temas"
          >
            Limpiar
          </button>

          <button
            type="button"
            id="aceptar-temas"
          >
            Aceptar
          </button>

        </div>

      </div>

    </div>
  <div class="estante-wrap">

    <div id="estante-3d">

      <!-- =====================================================
           BOTÓN DE INFORMACIÓN
           ===================================================== -->

      <button id="boton-info-libro"
              class="boton-info-libro"
              aria-label="Ver información">
        ˅
      </button>


      <!-- =====================================================
           TARJETA DE INFORMACIÓN
           ===================================================== -->

      <!-- Tarjeta de información -->
      <!-- Tarjeta de información -->
            <!-- =====================================================
           TARJETA DE INFORMACIÓN
           ===================================================== -->

      <div id="info-libro" class="info-libro">

        <!-- Botón LEER -->
        <a id="boton-leer-libro"
           class="boton-leer-libro"
           href="#"
           target="_blank"
           rel="noopener">
          <span>LEER</span>
        </a>

        <div class="info-libro-contenido">

          <!-- Portada -->
          <div class="info-portada-container">
            <img id="info-portada" src="" alt="Portada del libro">
          </div>

          <!-- Datos -->
          <div class="info-datos">

            <h2 id="info-titulo"></h2>

            <p class="info-autor" id="info-autor"></p>

            <!-- Descripción -->
            <div class="info-descripcion-container">
              <h3>Descripción</h3>
              <p id="info-descripcion"></p>
            </div>

            <!-- Metadatos -->
            <div class="info-metadatos">

              <div>
                <span>Páginas</span>
                <strong id="info-paginas">—</strong>
              </div>

              <div>
                <span>Categoría</span>
                <strong id="info-categoria">—</strong>
              </div>

              <div>
                <span>Tema</span>
                <strong id="info-tema">—</strong>
              </div>

              <div>
                <span>Tipo</span>
                <strong id="info-tipo">—</strong>
              </div>

              <div>
                <span>Idioma</span>
                <strong id="info-idioma">—</strong>
              </div>

              <div>
                <span>Edición</span>
                <strong id="info-edicion">—</strong>
              </div>

              <div>
                <span>Ejemplares</span>
                <strong id="info-ejemplares">—</strong>
              </div>

              <div>
                <span>Prestados</span>
                <strong id="info-prestados">—</strong>
              </div>

              <div>
                <span>Disponibilidad</span>
                <strong id="info-disponibilidad">—</strong>
              </div>

            </div>

          </div>

        </div>

      </div>

      <!-- SIN RESULTADOS -->
      <p
        id="sin-resultados"
        class="sin-resultados"
        style="display:none;"
      >
        No se encontraron libros con esos filtros.
      </p>

    </div>

  </div>

<script>
  window.APP_URL = "<?= $URL ?>";
</script>

<!-- Three.js real (WebGL), vía import map -->
<script type="importmap">
{
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@0.165.0/build/three.module.js"
  }
}
</script>
<script type="module" src="<?= $URL ?>/user/assets/js/estanteria-3d.js"></script>

<?php include("../ai/chat_widget.php"); ?>