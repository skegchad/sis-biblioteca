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
#toast-success {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #198754;
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: entrar 0.4s ease, salir 0.5s ease 3s forwards;
}
#toast-success-eliminado {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #ff0000;
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: entrar 0.4s ease, salir 0.5s ease 3s forwards;
}

@keyframes entrar {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

@keyframes salir {
    from { transform: translateX(0);    opacity: 1; }
    to   { transform: translateX(120%); opacity: 0; }
}
.camera_prev > span::before,
.camera_next > span::before {
    border-color: #0d6efd !important;
}

.camera_prev > span,
.camera_next > span {
    color: #0d6efd !important;
    background-color: #0d6efd !important;
}

.camera_prev:hover span::before,
.camera_next:hover span::before,
.camera_prev:hover span,
.camera_next:hover span {
    border-color: #0d6efd !important;
    background-color: #0d6efd !important;
}
</style>


    <!-- end header -->
    <?php if(isset($_GET['success']) && $_GET['success'] === 'noticia'): ?>
    <div id="toast-success">
        ¡Noticia registrada!
    </div>
    <?php endif; ?>
    <!-- end header -->
    <?php if(isset($_GET['success']) && $_GET['success'] === 'eliminada'): ?>
    <div id="toast-success-eliminado">
        ¡Noticia eliminada!
    </div>
    <?php endif; ?>


    <div class="banner-biblioteca-wrapper" id="bannerWrapper">

        <div class="banner-slides-track" id="slidesTrack">
            <div class="banner-slide" data-file="libross.jpeg" style="background-image: linear-gradient(rgba(42, 0, 192, 0.3), rgba(255, 0, 0, 0.3)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/libross.jpeg?v=<?php echo filemtime($ROOT . 'public/assets/img/grupoProyecto/libross.jpeg'); ?>');"></div>

            <div class="banner-slide" data-file="libross2.jpeg" style="background-image: linear-gradient(rgba(42, 0, 192, 0.3), rgba(255, 0, 0, 0.3)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/libross2.jpeg?v=<?php echo filemtime($ROOT . 'public/assets/img/grupoProyecto/libross2.jpeg'); ?>');"></div>
        </div>

        <span class="texto-banner">Biblioteca P. Luigi Ghezzi</span>

        <?php if ($cargo === 'Administrador'): ?>
            <div class="banner-click-overlay" onclick="document.getElementById('inputBanner').click()"></div>
            <input type="file" id="inputBanner" accept="image/*" style="display:none">
        <?php endif; ?>

        <button class="banner-arrow banner-arrow-left" onclick="cambiarSlide(-1)">&#10094;</button>
        <button class="banner-arrow banner-arrow-right" onclick="cambiarSlide(1)">&#10095;</button>

    </div>

    
    </div>

    <section class="seccion-catalogo">

        <h2 class="catalogo-titulo"><strong>Catálogo</strong></h2>

        <div class="catalogo-contenedor">
          <?php
          $query_categorias = $pdo->prepare('SELECT * FROM categorias');
          $query_categorias->execute();
          $categorias = $query_categorias->fetchAll(PDO::FETCH_ASSOC);
          foreach ($categorias as $categoria):
            $nombre_categoriaa = $categoria['nombre'];
            $foto = $categoria['foto'];
          ?>  
            <a href="catalogo.php?categoria=<?php echo trim($nombre_categoriaa);?>"><div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0)), url('<?php echo $URL; ?>/<?php echo trim($foto); ?>');"></div></a>
          <?php endforeach;?>
          <?php if($cargo=="Administrador"):?>
            <a href="<?php echo $URL?>/admin/libros/categorias/create.php"><div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/add_new.jpg');"></div></a>
          <?php endif;?>

        </div>

    </section>
    <!-- section featured -->

    <section id="featured">
      <h2 class="catalogo-titulo"><strong>Noticias</strong></h2>

      <?php if ($cargo == 'Administrador'): ?>
        <form id="uploadForm"
              action="<?= $URL; ?>/admin/noticias_controller_create.php"
              method="POST"
              enctype="multipart/form-data">
            <input
                type="file"
                id="imagen"
                name="imagen"
                accept="image/*"
                hidden>
        </form>
      <?php endif; ?>

      <!-- slideshow start here -->
      <div class="camera_wrap" id="camera-slide">
        <?php
          $query_noticias = $pdo->prepare('SELECT * FROM noticias');
          $query_noticias->execute();
          $noticias = $query_noticias->fetchAll(PDO::FETCH_ASSOC);
          foreach ($noticias as $noticia):
            $ruta_fotoo = $noticia['ruta_foto'];
            $id_noticia = $noticia['id_noticia'];
        ?>
          <div data-src="<?php echo $URL; ?>/<?php echo $ruta_fotoo; ?>">
            <?php if ($cargo == 'Administrador'): ?>
              <div class="camera_caption custom-caption">
                <span class="delete-news-btn" data-id="<?= $id_noticia; ?>" title="Eliminar noticia">&times;</span>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <?php if ($cargo == 'Administrador'): ?>
          <div data-src="<?= $URL; ?>/public/assets/img/grupoProyecto/add_new.jpg">
            <div class="camera_caption custom-caption">
              <a href="#" id="addNewSlide" class="add-new-slide-link"></a>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <!-- slideshow end here -->

    </section>
    <!-- /section featured -->

    <section id="works">
      <div class="container">
        <div class="row">
          <div class="span12">
            <h4 class="title"><strong>Tipos</strong> y <strong>TEMAS</strong></h4>
            <?php 
              $query_tipos = $pdo->prepare('SELECT id, nombre FROM tipos');
              $query_tipos->execute();
              $listaTipos=$query_tipos->fetchAll(PDO::FETCH_ASSOC);
              $vueltas=round(count($listaTipos)/4);
              $tipoActual=0;
              for ( $i=0 ;$i<$vueltas;$i++):
            ?>
              <div class="row">
                  <?php
                        $recorrido=4;
                        if($i==$vueltas-1 && count($listaTipos)%4!=0){$recorrido=count($listaTipos)%4;}
                        for ($j = 0; $j < $recorrido; $j++){
                        $tipo = $listaTipos[$tipoActual];
                        $id_tipo = $tipo['id'];
                        $nombreTipo = $tipo['nombre'];
                        $tipoActual++; 
                        $query_temas=$pdo->prepare('SELECT id, tipo_id, nombre FROM temas WHERE tipo_id=:tipo_id');
                        $query_temas->bindParam('tipo_id', $id_tipo, PDO::PARAM_INT);
                        $query_temas->execute();
                        $listaTemas=$query_temas->fetchAll(PDO::FETCH_ASSOC);  ?>
                    <div class="grid cs-style-4">
                      <div class="span3">
                        <div class="item item-tipos" >
                          <div>
                            <center>
                            <br>
                            <h3 style="color: aliceblue; font-weight: bold;"><?php echo $nombreTipo;?></h3>
                            </center>
                            <div class="contenedor-temas">
                              <ul class="celda-temas">
                                <?php foreach($listaTemas as $tema): ?>
                                <li style="color: black;"><a style="color: aliceblue" href="<?php echo $URL;?>/user/catalogo.php?tema=<?php echo $tema['nombre']; ?>"><?php echo $tema['nombre'];?></a></li>
                                <?php endforeach; ?>
                              </ul>
                            </div>
                          </div>                          
                        </div>
                      </div>
                    </div>
                  <?php } ?>

                  <?php if ($i == $vueltas-1 && $cargo === 'Administrador'): ?>
                    <div class="grid cs-style-4">
                      <div class="span3">
                        <a href="<?php echo $URL ?>/admin/libros/tipos/create.php">
                          <div class="item item-tipos item-admin" style="background-image: url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/add_new.jpg')">
                          </div>
                        </a>
                      </div>
                    </div>
                  <?php endif; ?>
              </div>
            <?php
            endfor;?>

          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="container">
        <div class="row">
          <div class="span4">
            <div class="widget">
              <h5 class="widgetheading">Browse pages</h5>
              <ul class="link-list">
                <li><a href="#">Our company</a></li>
                <li><a href="#">Terms and conditions</a></li>
                <li><a href="#">Privacy policy</a></li>
                <li><a href="#">Press release</a></li>
                <li><a href="#">What we have done</a></li>
                <li><a href="#">Our support forum</a></li>
              </ul>

            </div>
          </div>
          <div class="span4">
            <div class="widget">
              <h5 class="widgetheading">Get in touch</h5>
              <address>
							<strong>Eterna company Inc.</strong><br>
							Somestreet 200 VW, Suite Village A.001<br>
							Jakarta 13426 Indonesia
						</address>
              <p>
                <i class="icon-phone"></i> (123) 456-7890 - (123) 555-7891 <br>
                <i class="icon-envelope-alt"></i> email@domainname.com
              </p>
            </div>
          </div>
          <div class="span4">
            <div class="widget">
              <h5 class="widgetheading">Subscribe newsletter</h5>
              <p>
                Keep updated for new releases and freebies. Enter your e-mail and subscribe to our newsletter.
              </p>
              <form class="subscribe">
                <div class="input-append">
                  <input class="span2" id="appendedInputButton" type="text">
                  <button class="btn btn-theme" type="submit">Subscribe</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div id="sub-footer">
        <div class="container">
          <div class="row">
            <div class="span6">
              <div class="copyright">
                <p><span>&copy; Eterna company. All right reserved</span></p>
              </div>

            </div>

            <div class="span6">
              <div class="credits">
                <!--
                  All the links in the footer should remain intact.
                  You can delete the links only if you purchased the pro version.
                  Licensing information: https://bootstrapmade.com/license/
                  Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Eterna
                -->
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
  <a href="#" class="scrollup"><i class="icon-angle-up icon-square icon-bglight icon-2x active"></i></a>

  <!-- javascript
    ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="<?php echo $URL; ?>/public/js/jquery.js"></script>
  <script src="<?php echo $URL; ?>/public/js/jquery.easing.1.3.js"></script>
  <script src="<?php echo $URL; ?>/public/js/bootstrap.js"></script>

  <script src="<?php echo $URL; ?>/public/js/modernizr.custom.js"></script>
  <script src="<?php echo $URL; ?>/public/js/toucheffects.js"></script>
  <script src="<?php echo $URL; ?>/public/js/google-code-prettify/prettify.js"></script>
  <script src="<?php echo $URL; ?>/public/js/jquery.bxslider.min.js"></script>
  <script src="<?php echo $URL; ?>/public/js/camera/camera.js"></script>
  <script src="<?php echo $URL; ?>/public/js/camera/setting.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function () {

      const input = document.getElementById("imagen");
      const uploadForm = document.getElementById("uploadForm");

      // --- Eliminar noticia ---
      $(document).on("click", "#camera-slide .delete-news-btn", function (e) {
          e.preventDefault();
          e.stopPropagation();

          const idNoticia = $(this).data("id");
          if (!idNoticia) return;

          Swal.fire({
              title: '¿Eliminar esta noticia?',
              text: "Esta acción no se puede deshacer.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#d33'
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = "<?= $URL; ?>/admin/noticias_controller_erase.php?id=" + idNoticia;
              }
          });
      });

      // --- Agregar noticia ---
      if (input) {

          $(document).on("click", "#addNewSlide", function (e) {
              e.preventDefault();
              e.stopPropagation();

              Swal.fire({
                  title: 'Agregar imagen',
                  html: `
                      <p>Para obtener un mejor resultado en el carrusel:</p>
                      <ul style="text-align:left;display:inline-block;">
                          <li>📐 Relación de aspecto recomendada: <b>4:1</b>.</li>
                          <li>🖼️ Formatos permitidos: JPG, PNG o WEBP.</li>
                          <li>📦 Tamaño máximo: 5 MB.</li>
                      </ul>
                  `,
                  icon: 'info',
                  confirmButtonText: 'Entendido'
              }).then((result) => {
                  if (result.isConfirmed) {
                      input.click();
                  }
              });
          });

          input.addEventListener("change", function () {
              if (this.files.length > 0) {
                  uploadForm.submit();
              }
          });
      }

  });
/**
 * Sobreescribe el height, el centrado de imágenes y el tamaño de las
 * flechas del plugin "Camera" (camera_wrap).
 *
 * ENFOQUE: en vez de pelear en JS contra el loop de animación del plugin
 * (lo cual genera carreras / resultados intermitentes), inyectamos una
 * hoja de estilos con !important. El navegador aplica el cascade de CSS
 * en cada repintado, así que estas reglas SIEMPRE ganan, sin importar
 * cuándo el plugin toque el style inline durante sus animaciones.
 *
 * Cómo usarlo:
 * 1. Ajustá RELACION_ANCHO_ALTO si cambiás el ratio de tus imágenes.
 * 2. Incluí este archivo con <script> DESPUÉS del script del plugin Camera
 *    y después de donde llames a .camera().
 */
(function ($) {
    "use strict";

    var SELECTOR_WRAP = "#camera-slide"; // el contenedor de tu slideshow

    // Relación ancho:alto recomendada para las imágenes de "noticias".
    // Con tu medida de referencia (1351x539) da aprox 2.5 (osea 5:2).
    var RELACION_ANCHO_ALTO = 6 / 3;

    // Ancho de referencia: a este ancho de contenedor, las flechas se ven
    // a su tamaño original de diseño (40x40px, escala 1).
    var ANCHO_BASE_FLECHAS = 1351;
    var ESCALA_MIN = 0.5; // no dejar que se achiquen más de esto
    var ESCALA_MAX = 1;   // no dejar que crezcan más de esto

    // ------------------------------------------------------------------
    // 1) HEIGHT → resuelto con CSS inyectado (aspect-ratio), sin JS.
    //    Ya NO forzamos el centrado/recorte de la imagen: el admin sube
    //    las imágenes en la proporción correcta y el plugin las muestra
    //    tal cual, empezando desde arriba-izquierda como el comportamiento
    //    original. Esto también deja 100% compatible fx: 'random'.
    // ------------------------------------------------------------------
    var css = ""
        + SELECTOR_WRAP + " {"
        + "  aspect-ratio: " + RELACION_ANCHO_ALTO + " !important;"
        + "  height: auto !important;"
        + "}"
        + SELECTOR_WRAP + " .camera_slides,"
        + SELECTOR_WRAP + " .cameraContent,"
        + SELECTOR_WRAP + " .camera_target,"
        + SELECTOR_WRAP + " .camera_overlayer,"
        + SELECTOR_WRAP + " .cameraSlide,"
        + SELECTOR_WRAP + " .camera_wrap {"
        + "  height: 100% !important;"
        + "}";

    var styleTag = document.createElement("style");
    styleTag.setAttribute("data-source", "override-camera-height");
    styleTag.appendChild(document.createTextNode(css));
    document.head.appendChild(styleTag);

    // ------------------------------------------------------------------
    // 2) FLECHAS prev/next → esto sí necesita JS porque el factor de
    //    escala depende de un cálculo (ancho actual / ancho base), pero
    //    NO corre en loop continuo: solo en load/resize, así que no hay
    //    carrera posible con la animación del plugin.
    // ------------------------------------------------------------------
    function escalarFlechas() {
        var wrap = document.querySelector(SELECTOR_WRAP);
        if (!wrap) return;

        var anchoActual = wrap.offsetWidth;
        var escala = anchoActual / ANCHO_BASE_FLECHAS;
        escala = Math.max(ESCALA_MIN, Math.min(ESCALA_MAX, escala));

        var iconos = wrap.querySelectorAll(".camera_prev > span, .camera_next > span");
        iconos.forEach(function (el) {
            el.style.setProperty("transform", "scale(" + escala.toFixed(2) + ")", "important");
            el.style.setProperty("transform-origin", "center center", "important");
        });
    }

    $(window).on("load", function () {
        escalarFlechas();
        setTimeout(escalarFlechas, 300);
        setTimeout(escalarFlechas, 1000);
    });

    $(window).on("resize", function () {
        setTimeout(escalarFlechas, 100);
    });
})(jQuery);

  let slideActual = 0;
  const track = document.getElementById('slidesTrack');
  const slides = document.querySelectorAll('.banner-slide');
  const totalSlides = slides.length;

  function moverTrack() {
      track.style.transform = `translateX(-${slideActual * 100}%)`;
  }

  function cambiarSlide(direccion) {
      slideActual = (slideActual + direccion + totalSlides) % totalSlides;
      moverTrack();
  }

  setInterval(() => cambiarSlide(1), 6000);

  document.getElementById('inputBanner')?.addEventListener('change', function () {
      if (!this.files.length) return;

      const slideActivo = slides[slideActual];
      const nombreArchivo = slideActivo.dataset.file;

      const formData = new FormData();
      formData.append('banner', this.files[0]);
      formData.append('archivo', nombreArchivo);

      fetch('<?php echo $URL; ?>/admin/banner/subir_banner.php', {
          method: 'POST',
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          if (data.success) {
              const nuevaUrl = "<?php echo $URL; ?>/public/assets/img/grupoProyecto/" + nombreArchivo + "?v=" + new Date().getTime();
              slideActivo.style.backgroundImage =
                  `linear-gradient(rgba(42, 0, 192, 0.3), rgba(255, 0, 0, 0.3)), url('${nuevaUrl}')`;
          } else {
              alert(data.message || 'Error al subir el banner');
          }
      })
      .catch(err => {
          console.error(err);
          alert('Ocurrió un error al subir la imagen');
      });

      this.value = '';
  });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?php echo $URL; ?>/public/js/jquery.prettyPhoto.js"></script>
  <script src="<?php echo $URL; ?>/public/js/portfolio/jquery.quicksand.js"></script>
  <script src="<?php echo $URL; ?>/public/js/portfolio/setting.js"></script>

  <script src="<?php echo $URL; ?>/public/js/jquery.flexslider.js"></script>
  <script src="<?php echo $URL; ?>/public/js/animate.js"></script>
  <script src="<?php echo $URL; ?>/public/js/inview.js"></script>

  <!-- Template Custom JavaScript File -->
  <script src="<?php echo $URL; ?>/public/js/custom.js"></script>
  <style>
    #addNewSlide {
    cursor: pointer;
    pointer-events: auto;
    position: relative;
    z-index: 10; /* por encima de overlays del plugin Camera */
  }
  .add-new-slide-trigger {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 5;
    cursor: pointer;
  }
  #featured {
      position: relative;
  }

  #camera-slide .cameraContent .camera_caption.custom-caption {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: none;
      padding: 0;
      margin: 0;
  }

  #camera-slide .add-new-slide-link {
      display: block;
      position: absolute;
      inset: 0;
      cursor: pointer;
      z-index: 10;
  }

  #camera-slide .delete-news-btn {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(0,0,0,0.6);
      color: #fff;
      font-size: 20px;
      line-height: 32px;
      text-align: center;
      cursor: pointer;
      z-index: 20;
      font-family: Arial, sans-serif;
  }

  #camera-slide .delete-news-btn:hover {
      background: rgba(200,0,0,0.85);
  }
    /* Las flechas del plugin Camera dependen de :hover (mouseenter) para
    hacerse visibles, lo cual no existe en dispositivos táctiles.
    Forzamos que sean visibles siempre en pantallas de mobile/tablet. */
  @media (max-width: 991px) {
      #camera-slide .camera_prev,
      #camera-slide .camera_next {
          opacity: 1 !important;
      }
  }

  .banner-biblioteca:hover{
    cursor: pointer;
  }

  .item-tipos{
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius:12px;
    background-size:cover;
    background-position:center;
    background-color: #0d6efd;
    transition:transform .25s;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
  }
  .item-tipos:hover{
    transform:scale(1.05);
  }
  .contenedor-temas {
    max-height: 190px;
    overflow-y: auto;
  }

  .celda-temas {
      padding-left: 20px;
  }
  </style>
</body>
</html>

<?php include("../ai/chat_widget.php"); ?>