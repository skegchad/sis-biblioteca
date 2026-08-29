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
    <p>
      El grosor del libro = número de páginas del PDF ·
      El color = color dominante de la portada ·
      Click en un libro para verlo de frente
    </p>
  </div>

  <div class="filtros">
    <select id="f-categoria"></select>

    <select id="f-tema"></select>

    <input
      id="f-buscar"
      type="text"
      placeholder="Buscar por título o autor…"
    >
  </div>


  <div class="estante-wrap">

    <div id="estante-3d">

      <!-- =====================================================
           BOTÓN DE INFORMACIÓN
           ===================================================== -->

      <button
        id="boton-info-libro"
        class="boton-info-libro"
        aria-label="Ver información"
        type="button"
      >
        ˅
      </button>


      <!-- =====================================================
           TARJETA DE INFORMACIÓN
           ===================================================== -->

      <!-- Tarjeta de información -->
      <!-- Tarjeta de información -->
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