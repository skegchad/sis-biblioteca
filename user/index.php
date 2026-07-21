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


    <!-- end header -->

    <div class="banner-biblioteca">
    </div>

    <section class="seccion-catalogo">

        <h2 class="catalogo-titulo">Catálogo</h2>

        <div class="catalogo-contenedor">
          <?php
          $query_categorias = $pdo->prepare('SELECT * FROM categorias');
          $query_categorias->execute();
          $categorias = $query_categorias->fetchAll(PDO::FETCH_ASSOC);
          foreach ($categorias as $categoria):
            $foto = $categoria['foto'];
          ?>  
            <a href="persona.html"><div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0)), url('<?php echo $URL; ?>/<?php echo trim($foto); ?>');"></div></a>
          <?php endforeach;?>

        </div>

    </section>
    <!-- section featured -->

    <section id="featured">
      <h2 class="catalogo-titulo">Noticias</h2>
      <!-- slideshow start here -->

      <div class="camera_wrap" id="camera-slide">

        <!-- slide 1 here -->
        <div data-src="<?php echo $URL; ?>/public/uploads/img/noticias/noticia1.jpg">
        </div>

        <!-- slide 2 here -->
        <div data-src="<?php echo $URL; ?>/public/uploads/img/noticias/noticia2.jpg">
        </div>

        <!-- slide 3 here -->
        <div data-src="<?php echo $URL; ?>/public/uploads/img/noticias/noticia3.jpg">
        </div>

      </div>

      <!-- slideshow end here -->

    </section>
    <!-- /section featured -->

    <section id="content">
      <div class="container">


        <div class="row">
          <div class="span12">
            <div class="row">
              <div class="span4">
                <div class="box flyLeft">
                  <div class="icon">
                    <i class="ico icon-circled icon-bgdark icon-star active icon-3x"></i>
                  </div>
                  <div class="text">
                    <h4>High <strong>Quality</strong></h4>
                    <p>
                      Lorem ipsum dolor sit amet, has ei ipsum scaevola deseruisse am sea facilisis.
                    </p>
                    <a href="#">Learn More</a>
                  </div>
                </div>
              </div>

              <div class="span4">
                <div class="box flyIn">
                  <div class="icon">
                    <i class="ico icon-circled icon-bgdark icon-dropbox active icon-3x"></i>
                  </div>
                  <div class="text">
                    <h4>Rich of <strong>Features</strong></h4>
                    <p>
                      Lorem ipsum dolor sit amet, has ei ipsum scaevola deseruisse am sea facilisis.
                    </p>
                    <a href="#">Learn More</a>
                  </div>
                </div>
              </div>
              <div class="span4">
                <div class="box flyRight">
                  <div class="icon">
                    <i class="ico icon-circled icon-bgdark icon-laptop active icon-3x"></i>
                  </div>
                  <div class="text">
                    <h4>Modern <strong>Design</strong></h4>
                    <p>
                      Lorem ipsum dolor sit amet, has ei ipsum scaevola deseruisse am sea facilisis.
                    </p>
                    <a href="#">Learn More</a>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="row">
          <div class="span12">
            <div class="solidline"></div>
          </div>
        </div>

        <div class="row">
          <div class="span12">
            <div class="row">
              <div class="span12">
                <div class="aligncenter">
                  <h3>Our <strong>Pricing</strong></h3>
                  <p>Lorem ipsum dolor sit amet, labores dolorum scriptorem eum an, te quodsi sanctus neglegentur.
                  </p>
                </div>
              </div>
            </div>


            <div class="row">

              <div class="span3">
                <div class="pricing-box-wrap animated-fast flyIn">
                  <div class="pricing-heading">
                    <h3>Very <strong>Basic</strong></h3>
                  </div>
                  <div class="pricing-terms">
                    <h6>&#36;15.00 / Month</h6>
                  </div>
                  <div class="pricing-content">
                    <ul>
                      <li><i class="icon-ok"></i> 100 applications</li>
                      <li><i class="icon-ok"></i> 24x7 support available</li>
                      <li><i class="icon-ok"></i> No hidden fees</li>
                      <li><i class="icon-ok"></i> Free 30-days trial</li>
                      <li><i class="icon-ok"></i> Stop anytime easily</li>
                    </ul>
                  </div>
                  <div class="pricing-action">
                    <a href="#" class="btn btn-medium btn-theme"><i class="icon-chevron-down"></i> Sign Up</a>
                  </div>
                </div>
              </div>

              <div class="span3">
                <div class="pricing-box-wrap animated-fast flyIn">
                  <div class="pricing-heading">
                    <h3>Simple <strong>Choice</strong></h3>
                  </div>
                  <div class="pricing-terms">
                    <h6>&#36;20.00 / Month</h6>
                  </div>
                  <div class="pricing-content">
                    <ul>
                      <li><i class="icon-ok"></i> 100 applications</li>
                      <li><i class="icon-ok"></i> 24x7 support available</li>
                      <li><i class="icon-ok"></i> No hidden fees</li>
                      <li><i class="icon-ok"></i> Free 30-days trial</li>
                      <li><i class="icon-ok"></i> Stop anytime easily</li>
                    </ul>
                  </div>
                  <div class="pricing-action">
                    <a href="#" class="btn btn-medium btn-theme"><i class="icon-chevron-down"></i> Sign Up</a>
                  </div>
                </div>
              </div>

              <div class="span3">
                <div class="pricing-box-wrap special animated-slow flyIn">
                  <div class="pricing-heading">
                    <h3>Special <strong>Choice</strong></h3>
                  </div>
                  <div class="pricing-terms">
                    <h6>&#36;15.00 / Month</h6>
                  </div>
                  <div class="pricing-content">
                    <ul>
                      <li><i class="icon-ok"></i> 100 applications</li>
                      <li><i class="icon-ok"></i> 24x7 support available</li>
                      <li><i class="icon-ok"></i> No hidden fees</li>
                      <li><i class="icon-ok"></i> Free 30-days trial</li>
                      <li><i class="icon-ok"></i> Stop anytime easily</li>
                    </ul>
                  </div>
                  <div class="pricing-action">
                    <a href="#" class="btn btn-medium btn-theme"><i class="icon-chevron-down"></i> Sign Up</a>
                  </div>
                </div>
              </div>

              <div class="span3">
                <div class="pricing-box-wrap animated flyIn">
                  <div class="pricing-heading">
                    <h3>Just <strong>Happy</strong></h3>
                  </div>
                  <div class="pricing-terms">
                    <h6>&#36;15.00 / Month</h6>
                  </div>
                  <div class="pricing-content">
                    <ul>
                      <li><i class="icon-ok"></i> 100 applications</li>
                      <li><i class="icon-ok"></i> 24x7 support available</li>
                      <li><i class="icon-ok"></i> No hidden fees</li>
                      <li><i class="icon-ok"></i> Free 30-days trial</li>
                      <li><i class="icon-ok"></i> Stop anytime easily</li>
                    </ul>
                  </div>
                  <div class="pricing-action">
                    <a href="#" class="btn btn-medium btn-theme"><i class="icon-chevron-down"></i> Sign Up</a>
                  </div>
                </div>
              </div>
            </div>

          </div>


        </div>



        <div class="row">
          <div class="span12 aligncenter">
            <h3 class="title">What people <strong>saying</strong> about us</h3>
            <div class="blankline30"></div>

            <ul class="bxslider">
              <li>
                <blockquote>
                  Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis feugiat.Suspendisse eu erat quam. Vivamus porttitor eros quis nisi lacinia sed interdum lorem vulputate. Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis
                  feugiat
                </blockquote>
                <div class="testimonial-autor">
                  <img src="<?php echo $URL; ?>/public/assets/img/dummies/testimonial/1.png" alt="" />
                  <h4>Hillary Doe</h4>
                  <a href="#">www.companyname.com</a>
                </div>
              </li>
              <li>
                <blockquote>
                  Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis feugiat.Suspendisse eu erat quam. Vivamus porttitor eros quis nisi lacinia sed interdum lorem vulputate. Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis
                  feugiat
                </blockquote>
                <div class="testimonial-autor">
                  <img src="<?php echo $URL; ?>/public/assets/img/dummies/testimonial/2.png" alt="" />
                  <h4>Michael Doe</h4>
                  <a href="#">www.companyname.com</a>
                </div>
              </li>
              <li>
                <blockquote>
                  Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis feugiat.Suspendisse eu erat quam. Vivamus porttitor eros quis nisi lacinia sed interdum lorem vulputate. Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis
                  feugiat
                </blockquote>
                <div class="testimonial-autor">
                  <img src="<?php echo $URL; ?>/public/assets/img/dummies/testimonial/3.png" alt="" />
                  <h4>Mark Donovan</h4>
                  <a href="#">www.companyname.com</a>
                </div>
              </li>
              <li>
                <blockquote>
                  Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis feugiat.Suspendisse eu erat quam. Vivamus porttitor eros quis nisi lacinia sed interdum lorem vulputate. Aliquam a orci quis nisi sagittis sagittis. Etiam adipiscing, justo quis
                  feugiat
                </blockquote>
                <div class="testimonial-autor">
                  <img src="<?php echo $URL; ?>/public/assets/img/dummies/testimonial/4.png" alt="" />
                  <h4>Marry Doe Elliot</h4>
                  <a href="#">www.companyname.com</a>
                </div>
              </li>
            </ul>

          </div>
        </div>

      </div>
    </section>


    <section id="works">
      <div class="container">
        <div class="row">
          <div class="span12">
            <h4 class="title">Recent <strong>Works</strong></h4>
            <div class="row">

              <div class="grid cs-style-4">
                <div class="span3">
                  <div class="item">
                    <figure>
                      <div><img src="<?php echo $URL; ?>/public/assets/img/dummies/works/1.jpg" alt="" /></div>
                      <figcaption>
                        <div>
                          <span>
								<a href="<?php echo $URL; ?>/public/assets/img/dummies/works/big.png" data-pretty="prettyPhoto[gallery1]" title="Portfolio caption here"><i class="icon-plus icon-circled icon-bglight icon-2x"></i></a>
								</span>
                          <span>
								<a href="#"><i class="icon-file icon-circled icon-bglight icon-2x"></i></a>
								</span>
                        </div>
                      </figcaption>
                    </figure>
                  </div>
                </div>
                <div class="span3">
                  <div class="item">
                    <figure>
                      <div><img src="<?php echo $URL; ?>/public/assets/img/dummies/works/2.jpg" alt="" /></div>
                      <figcaption>
                        <div>
                          <span>
								<a href="<?php echo $URL; ?>/public/assets/img/dummies/works/big.png" data-pretty="prettyPhoto[gallery1]" title="Portfolio caption here"><i class="icon-plus icon-circled icon-bglight icon-2x"></i></a>
								</span>
                          <span>
								<a href="#"><i class="icon-file icon-circled icon-bglight icon-2x"></i></a>
								</span>
                        </div>
                      </figcaption>
                    </figure>
                  </div>
                </div>
                <div class="span3">
                  <div class="item">
                    <figure>
                      <div><img src="<?php echo $URL; ?>/public/assets/img/dummies/works/3.jpg" alt="" /></div>
                      <figcaption>
                        <div>
                          <span>
								<a href="<?php echo $URL; ?>/public/assets/img/dummies/works/big.png" data-pretty="prettyPhoto[gallery1]" title="Portfolio caption here"><i class="icon-plus icon-circled icon-bglight icon-2x"></i></a>
								</span>
                          <span>
								<a href="#"><i class="icon-file icon-circled icon-bglight icon-2x"></i></a>
								</span>
                        </div>
                      </figcaption>
                    </figure>
                  </div>
                </div>
                <div class="span3">
                  <div class="item">
                    <figure>
                      <div><img src="<?php echo $URL; ?>/public/assets/img/dummies/works/4.jpg" alt="" /></div>
                      <figcaption>
                        <div>
                          <span>
								<a href="<?php echo $URL; ?>/public/assets/img/dummies/works/big.png" data-pretty="prettyPhoto[gallery1]" title="Portfolio caption here"><i class="icon-plus icon-circled icon-bglight icon-2x"></i></a>
								</span>
                          <span>
								<a href="#"><i class="icon-file icon-circled icon-bglight icon-2x"></i></a>
								</span>
                        </div>
                      </figcaption>
                    </figure>
                  </div>
                </div>
              </div>

            </div>
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

    
  (function ($) {
      "use strict";
  
      var SELECTOR_WRAP = "#camera-slide"; // el contenedor de tu slideshow
  
      // Opción A: altura fija en px. Ejemplo: 500
      // Opción B: altura como % del alto de la ventana (equivalente a vh). Ejemplo: "50%"
      var ALTURA_DESEADA = "90%";
  
      // Solo se usan si ALTURA_DESEADA es un porcentaje (opcionales)
      var ALTURA_MIN_PX = 300;
      var ALTURA_MAX_PX = 800;
  
      function calcularAlturaPx() {
          if (typeof ALTURA_DESEADA === "number") {
              return ALTURA_DESEADA;
          }
          // es un string tipo "50%"
          var porcentaje = parseFloat(ALTURA_DESEADA) / 100;
          var alturaPx = window.innerHeight * porcentaje;
  
          if (ALTURA_MIN_PX) alturaPx = Math.max(alturaPx, ALTURA_MIN_PX);
          if (ALTURA_MAX_PX) alturaPx = Math.min(alturaPx, ALTURA_MAX_PX);
  
          return Math.round(alturaPx);
      }
  
      function forzarAltura() {
          var wrap = document.querySelector(SELECTOR_WRAP);
          if (!wrap) return;
  
          var alturaPx = calcularAlturaPx() + "px";
  
          // jQuery.css() NO soporta !important, por eso el plugin lo seguía
          // pisando. Usamos setProperty con el tercer parámetro "important".
          wrap.style.setProperty("height", alturaPx, "important");
  
          wrap.querySelectorAll(".camera_slides, .cameraContent, .camera_wrap")
              .forEach(function (el) {
                  el.style.setProperty("height", alturaPx, "important");
              });
      }
  
      // 1) Al terminar de cargar todo (imágenes incluidas, que es cuando
      //    el plugin suele recalcular su altura por primera vez)
      $(window).on("load", function () {
          forzarAltura();
          // el plugin a veces recalcula con un pequeño delay tras el load,
          // así que reforzamos un par de veces más por las dudas
          setTimeout(forzarAltura, 300);
          setTimeout(forzarAltura, 1000);
      });
  
      // 2) Cada vez que el plugin recalcula por resize
      $(window).on("resize", function () {
          setTimeout(forzarAltura, 100);
      });
  
      // 3) Vigilante: si el plugin vuelve a tocar el style en cualquier
      //    momento (transición entre slides, etc.), lo corregimos al toque.
      var nodo = document.querySelector(SELECTOR_WRAP);
      if (nodo && window.MutationObserver) {
          var observer = new MutationObserver(function (mutaciones) {
              mutaciones.forEach(function (m) {
                  if (m.attributeName === "style") {
                      var alturaActual = nodo.style.height;
                      var alturaEsperada = calcularAlturaPx() + "px";
                      if (alturaActual !== alturaEsperada) {
                          forzarAltura();
                      }
                  }
              });
          });
          observer.observe(nodo, { attributes: true, attributeFilter: ["style"] });
      }
  })(jQuery);
  </script>
  <script src="<?php echo $URL; ?>/public/js/jquery.prettyPhoto.js"></script>
  <script src="<?php echo $URL; ?>/public/js/portfolio/jquery.quicksand.js"></script>
  <script src="<?php echo $URL; ?>/public/js/portfolio/setting.js"></script>

  <script src="<?php echo $URL; ?>/public/js/jquery.flexslider.js"></script>
  <script src="<?php echo $URL; ?>/public/js/animate.js"></script>
  <script src="<?php echo $URL; ?>/public/js/inview.js"></script>

  <!-- Template Custom JavaScript File -->
  <script src="<?php echo $URL; ?>/public/js/custom.js"></script>

</body>
</html>

<?php include("../ai/chat_widget.php"); ?>