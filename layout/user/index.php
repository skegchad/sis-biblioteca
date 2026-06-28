<?php

include ('../../app/config/config.php');
include ('../../app/config/conexion.php');
?>
<!DOCTYPE html>
<html lang="en">

<style>
        .banner-biblioteca {
            /* Ajusta la altura que prefieras para tu banner */
            height: 250px; 
            width: 1200px;
            margin:auto;
            margin-top: 40px;
            /* 1. Ponemos un fondo oscuro semitransparente (linear-gradient) para que el texto resalte.
               2. Ponemos la imagen de fondo. ¡REEMPLAZA 'tu-imagen-aqui.jpg' por la ruta real de tu foto de libros! */
            background-image: linear-gradient(rgba(42, 0, 192, 0.3), rgba(255, 0, 0, 0.3)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/libross.jpeg');
            background-size:cover; /* Hace que la imagen cubra todo el espacio sin deformarse */
            background-position: center; /* Centra la imagen de fondo */
            
        }
        .seccion-catalogo{
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .catalogo-titulo{
            text-align: center;
            margin-bottom: 30px;
        }

        .catalogo-contenedor{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .catalogo-tarjeta{
            height: 280px;
            border-radius: 10px;
            background-size: cover;
            background-position: center;
            cursor: pointer;
            transition: transform .3s ease;
        }

        .catalogo-tarjeta:hover{
            transform: scale(1.05);
        }
</style>


<head>
  <meta charset="utf-8">
  <title>BIBLIOTECA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Your page description here" />
  <meta name="author" content="" />
  <link rel="icon" href="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" type="image/png">

  <!-- css -->
  <link href="https://fonts.googleapis.com/css?family=Handlee|Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="<?php echo $URL; ?>/public/css/bootstrap.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/bootstrap-responsive.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/flexslider.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/prettyPhoto.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/camera.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/jquery.bxslider.css" rel="stylesheet" />
  <link href="<?php echo $URL; ?>/public/css/style.css" rel="stylesheet" />

  <!-- Theme skin -->
  <link href="<?php echo $URL; ?>/public/color/default.css" rel="stylesheet" />

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $URL; ?>/ico/apple-touch-icon-144-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $URL; ?>/ico/apple-touch-icon-114-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $URL; ?>/ico/apple-touch-icon-72-precomposed.png" />
  <link rel="apple-touch-icon-precomposed" href="<?php echo $URL; ?>/ico/apple-touch-icon-57-precomposed.png" />
  <link rel="shortcut icon" href="<?php echo $URL; ?>/ico/favicon.png" />

  <!-- =======================================================
    Theme Name: Eterna
    Theme URL: https://bootstrapmade.com/eterna-free-multipurpose-bootstrap-template/
    Author: BootstrapMade.com
    Author URL: https://bootstrapmade.com
  ======================================================= -->
  <style>
    header{
            background-color: #0d6efd; /* Azul Bootstrap */
        }

        /* Barra de navegación */
        .navbar{
            background: transparent;
        }

        /* Color de los enlaces */
        .nav.topnav > li > a{
            color: white !important;
            font-weight: bold;
        }

        /* Color al pasar el mouse */
        .nav.topnav > li > a:hover{
            color: #dbeafe !important;
        }

        /* Logo */
        .logo{
            padding: 10px 0;
        }
        .nav.topnav > li.active > a{
            background: #1e3a8a !important; /* Mismo azul del header */
            color: #fff !important;
            border-radius: 5px;
        }

        /* Hover */
        .nav.topnav > li > a:hover{
            background: #2952a3 !important;
            color: #fff !important;
        }
        .logo-contenedor{
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
        }

        .logo-contenedor img{
            height: 100px; /* Ajusta el tamaño del logo */
            width: auto;
        }

        .titulo-biblioteca{
            margin: 0;
            color: white;
            font-size: 40px;
            font-weight: bold;
            letter-spacing: 0px;
            font-family: 'Open Sans', sans-serif;
        }
        .logo-contenedor{
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
        }

        .logo-contenedor img{
            height: 70px;
            width: auto;
        }

        .titulo-biblioteca{
            color: #fff;
            margin: 0;
            font-size: 30px;
            font-weight: bold;
        }

        /* Centrar la navegación verticalmente */
        .span8{
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-height: 90px;
        }

        .navbar{
            margin: 0;
        }
  </style>
</head>

<body>

  <div id="wrapper">

    <!-- start header -->
    <header>
      <div class="container">


        <div class="row nomargin">
          <div class="span4">
              <div class="logo-contenedor">
                  <a href="#">
                      <img src="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" alt="Logo">
                  </a>
                  <a href="#">
                  <h1 class="titulo-biblioteca">BIBLIOTECA</h1>
                  </a>
              </div>
          </div>
          <div class="span8">
            <div class="navbar navbar-static-top">
              <div class="navigation">
                <nav>
                  <ul class="nav topnav">
                    <li class="active">
                      <a href="#"><i class="icon-home"></i> Inicio </a>
                    </li>
                    <li>
                      <a href="<?php echo $URL; ?>/app/templeates/free boosk/index.html">Catalogación</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/app/templeates/Eterna/index-alt2.html">Prestamo</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/app/templeates/Eterna/contact.html">Contacto</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/layout/user/modelo bmo/modelo.html">Conoce a BMO</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/login">INICIAR SESIÓN</a>
                    </li>
                    
                  </ul>
                </nav>
              </div>
              <!-- end navigation -->
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- end header -->

    <div class="banner-biblioteca">
    </div>

    <section class="seccion-catalogo">

        <h2 class="catalogo-titulo">Catálogo</h2>

        <div class="catalogo-contenedor">

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/CIENCIAS SOCIALES.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/LITERATURA RETÓRICA.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/TECNOLOGÍA.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/RELIGIÓN.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/FILOSOFÍA Y PSICOLOGÍA.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/CIENCIAS NATURALES Y MATEMÁTICAS.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/GEOGRAFÍA E HISTORIA.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/LENGUAS.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/ARTES.png');"></div>

            <div class="catalogo-tarjeta" style="background-image: linear-gradient(to top, rgba(0,0,0,0.4), rgba(0,0,0,0)), url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/GENERALIDADES.png');"></div>

        </div>

    </section>
    <!-- section featured -->
    <section id="featured">

      <!-- slideshow start here -->

      <div class="camera_wrap" id="camera-slide">

        <!-- slide 1 here -->
        <div data-src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide1/img1.jpg">
          <div class="camera_caption fadeFromLeft">
            <div class="container">
              <div class="row">
                <div class="span6">
                  <h2 class="animated fadeInDown"><strong>Great template for <span class="colored">multi usage</span></strong></h2>
                  <p class="animated fadeInUp"> Vim porro dicam reprehendunt te, populo quodsi dissentiet cum ad. Ne natum deseruisse vis. Iisque deseruisse sententiae mel ne, dolores appetere vim ut. Sea no tamquam reprimique.</p>
                  <a href="#" class="btn btn-success btn-large animated fadeInUp">
											<i class="icon-link"></i> Read more
										</a>
                  <a href="#" class="btn btn-theme btn-large animated fadeInUp">
											<i class="icon-download"></i> Download
										</a>
                </div>
                <div class="span6">
                  <img src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide1/screen.png" alt="" class="animated bounceInDown delay1" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- slide 2 here -->
        <div data-src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide2/img1.jpg">
          <div class="camera_caption fadeFromLeft">
            <div class="container">
              <div class="row">
                <div class="span6">
                  <img src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide2/iMac.png" alt="" />
                </div>
                <div class="span6">
                  <h2 class="animated fadeInDown"><strong>Put your <span class="colored">Opt in form</span></strong></h2>
                  <p class="animated fadeInUp"> Vim porro dicam reprehendunt te, populo quodsi dissentiet cum ad. Ne natum deseruisse vis. Iisque deseruisse sententiae mel ne, dolores appetere vim ut. Sea no tamquam reprimique.</p>
                  <form>
                    <div class="input-append">
                      <input class="span3 input-large" type="text">
                      <button class="btn btn-theme btn-large" type="submit">Subscribe</button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>

        <!-- slide 3 here -->
        <div data-src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide2/img1.jpg">
          <div class="camera_caption fadeFromLeft">
            <div class="container">
              <div class="row">
                <div class="span12 aligncenter">
                  <h2 class="animated fadeInDown"><strong><span class="colored">Responsive</span> and <span class="colored">cross broswer</span> compatibility</strong></h2>
                  <p class="animated fadeInUp">Pellentesque habitant morbi tristique senectus et netus et malesuada</p>
                  <img src="<?php echo $URL; ?>/public/assets/img/slides/camera/slide3/browsers.png" alt="" class="animated bounceInDown delay1" />
                </div>
              </div>
            </div>
          </div>
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