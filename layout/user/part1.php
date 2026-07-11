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
    .header-principal{
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
    <header class="header-principal">
      <div class="container">


        <div class="row nomargin">
          <div class="span4">
              <div class="logo-contenedor">
                  <a href="<?php echo $URL; ?>/user">
                      <img src="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" alt="Logo">
                  </a>
                  <a href="<?php echo $URL; ?>/user">
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
                      <a href="<?php echo $URL; ?>/user"><i class="icon-home"></i> Inicio </a>
                    </li>
                    <li>
                      <a href="<?php echo $URL; ?>/layout/user/catalogo.php">Catalogación</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/app/templeates/Eterna/index-alt2.html">Prestamo</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/app/templeates/Eterna/contact.html">Contacto</a>
                    </li>

                    <li>
                      <a href="<?php echo $URL; ?>/layout/user/modelo bmo/modelo.php">Conoce a BMO</a>
                    </li>

                    <li>
                      <a href="<?php echo $rutaAdmin; ?>"><?php echo $msj;?></a>
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