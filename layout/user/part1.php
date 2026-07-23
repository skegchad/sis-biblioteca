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



        .catalogo-tarjeta:hover{
            transform: scale(1.05);
        }
        .header-principal{
            background-color: #0d6efd;
            position: relative;
        }

        .usuario-header{
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            margin-right: 20px;
        }

        .usuario-header a{
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .usuario-header img{
            width: 45px;
            height: 45px;
            object-fit: cover;
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
/* ===========================
   HEADER - RESPONSIVE
=========================== */

.header-principal{
    background:#0d6efd;
    box-shadow:0 2px 10px rgba(0,0,0,.15);
}

.header-contenido{
    display:flex;
    align-items:center;
    justify-content:space-between;
    height:100px;
    gap:30px;
}

/* ===========================
   LOGO
=========================== */

.logo-contenedor{
    gap:8px;
    display:flex;
    align-items: center;
    min-width: 0; /* permite que el flex-item se achique sin romper el layout */
}

.logo-contenedor img{
    height:70px;
    flex-shrink: 0;
}

.titulo-biblioteca{
    margin:0;
    color:#fff;
    font-size:30px;
    font-weight:bold;
    font-family:'Open Sans', sans-serif;
    white-space: nowrap;
}

/* ===========================
   BOTÓN HAMBURGUESA (oculto en desktop)
=========================== */

.btn-menu-movil{
    display:none;
    background:none;
    border:none;
    color:#fff;
    font-size:28px;
    cursor:pointer;
    padding: 5px 10px;
}

/* ===========================
   MENÚ
=========================== */

.menu-principal{
    flex:1;
    display:flex;
    justify-content:center;
}

.navbar{
    margin:0;
    background:transparent;
    box-shadow:none;
}

.nav.topnav{
    display:flex;
    align-items:center;
    gap:10px;
    margin:0;
    flex-wrap: wrap;
}

.nav.topnav li{
    list-style:none;
}

.nav.topnav li a{
    color:#fff !important;
    font-weight:bold;
    padding:10px 15px;
    border-radius:6px;
    transition:.25s;
    white-space: nowrap;
}

.nav.topnav li a:hover{
    background:#2952a3 !important;
}

.nav.topnav li.active a{
    background:#1d3f91 !important;
}

/* ===========================
   USUARIO
=========================== */

.usuario-header{
    flex-shrink:0;
    margin-right:15px;
}

.usuario-menu{
    position:relative;
}

.usuario-menu summary{
    display:flex;
    align-items:center;
    gap:10px;
    list-style:none;
    cursor:pointer;
    color:white;
    font-weight:bold;
    user-select:none;
}

.usuario-menu summary::-webkit-details-marker{
    display:none;
}

.usuario-menu img{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink: 0;
}

.usuario-menu summary::after{
    content:"▼";
    font-size:10px;
    margin-left:5px;
}

.menu-desplegable{
    position:absolute;
    top:60px;
    right:0;
    width:220px;
    background:white;
    border-radius:10px;
    box-shadow:0 8px 25px rgba(0,0,0,.18);
    overflow:hidden;
    z-index:999;
}

.menu-desplegable a{
    display:block;
    padding:12px 15px;
    text-decoration:none;
    color:#333;
    transition:.2s;
}

.menu-desplegable a:hover{
    background:#f4f6f8;
}

.menu-desplegable hr{
    margin:0;
    border:none;
    border-top:1px solid #ddd;
}

.cerrar{
    color:#d32f2f !important;
}

/* ===========================
   BANNER
=========================== */
.banner-biblioteca{
    width:100%;
    max-width:1200px;
    height:280px;
    margin:35px auto;
    border-radius:12px;
    background-image:
    linear-gradient(rgba(42,0,192,.25),rgba(255,0,0,.25)),
    url('<?php echo $URL; ?>/public/assets/img/grupoProyecto/libross.jpeg');
    background-size:cover;
    background-position:center;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.18);
}

/* ===========================
   CATÁLOGO
=========================== */

.seccion-catalogo{
    max-width:1200px;
    margin:40px auto;
    padding:0 20px;
}

.catalogo-titulo{
    text-align:center;
    margin-bottom:30px;
}

.catalogo-contenedor{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:25px;
}

.catalogo-tarjeta{
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius:12px;
    background-size:cover;
    background-position:center;
    cursor:pointer;
    transition:transform .25s;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
}

.catalogo-tarjeta:hover{
    transform:translateY(-5px);
}

/* ===========================
   RESPONSIVE - TABLET
=========================== */
@media (max-width: 992px){
    .titulo-biblioteca{
        font-size: 22px;
    }
    .nav.topnav li a{
        padding: 8px 10px;
        font-size: 14px;
    }
}

/* ===========================
   RESPONSIVE - MOBILE
=========================== */
@media (max-width: 768px){

    .header-contenido{
        height: auto;
        flex-wrap: wrap;
        padding: 15px 0;
        gap: 10px;
    }

    /* Logo ocupa su espacio, el usuario queda a la derecha, y el botón hamburguesa aparece */
    .logo-contenedor img{
        height: 45px;
    }

    .titulo-biblioteca{
        font-size: 18px;
    }

    .btn-menu-movil{
        display: block;
        order: 2;
    }

    .btn-menu-movil{
        z-index:10000;
    }

    .usuario-header{
        order: 3;
        margin-right: 0;
    }

    .usuario-menu img{
        width: 38px;
        height: 38px;
    }

    .usuario-menu summary span{
        display: none; /* oculta el nombre en mobile, deja solo la foto, para ahorrar espacio */
    }

    /* El menú se oculta por defecto y se muestra abajo, en toda la fila, al abrir */
    .menu-principal{
        position: fixed;
        top: 0;
        left: -280px;
        width: 260px;
        height: 100vh;
        background: #0d6efd;
        transition: .3s;
        z-index: 9999;
        padding-top: 90px;
        box-shadow: 5px 0 15px rgba(0,0,0,.3);
        overflow-y:auto;
    }

    .menu-principal.menu-abierto{
        left: 0;
    }

    .nav.topnav{
        display:flex !important;
        flex-direction:column;
        align-items:stretch;
        width:100%;
        margin:0;
        padding:0;
    }

    .nav.topnav li{
        display:block;
        width:100%;
        list-style:none;
    }

    .nav.topnav li a{
        display:block;
        width:100%;
        color:white !important;
        padding:18px 25px;
        text-decoration:none;
        border-bottom:1px solid rgba(255,255,255,.15);
    }

    .banner-biblioteca{
        height: 160px;
        margin: 20px auto;
    }

    .catalogo-contenedor{
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
}

@media (max-width: 480px){
    .titulo-biblioteca{
        display: none; /* en pantallas muy chicas, deja solo el ícono del logo */
    }

    .catalogo-contenedor{
        grid-template-columns: 1fr;
    }
}

#overlayMenu{
    position: fixed;
    top: 0;
    left: 260px; /* mismo ancho del sidebar */
    width: calc(100% - 260px);
    height: 100%;
    background: rgba(0,0,0,.45);
    opacity: 0;
    visibility: hidden;
    transition: .3s;
    z-index: 9998;
}

#overlayMenu.activo{
    opacity:1;
    visibility:visible;
}


</style>

<body>

  <div id="wrapper">

    <!-- start header -->
    <header class="header-principal">
        <div class="container">

            <div class="header-contenido">

                <!-- Logo -->
                <div class="logo-contenedor">
                    <a href="<?php echo $URL; ?>/user">
                        <img src="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" alt="Logo">
                    </a>

                    <a href="<?php echo $URL; ?>/user" style="text-decoration:none;">
                        <h1 class="titulo-biblioteca">BIBLIOTECA</h1>
                    </a>
                </div>

                <!-- Botón hamburguesa (solo visible en mobile) -->
                <button type="button" class="btn-menu-movil" id="btnMenuMovil">
                    ☰
                </button>

                <!-- Menú -->
                <nav class="menu-principal" id="menuPrincipal">
                    <ul class="nav topnav">

                        <li class="active">
                            <a href="<?php echo $URL; ?>/user">
                                <i class="icon-home"></i> Inicio
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo $URL; ?>/user/catalogo.php">
                                Catalogación
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                Préstamo
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                Contacto
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo $URL; ?>/layout/user/modelo bmo/modelo.php">
                                Conoce a BMO
                            </a>
                        </li>

                    </ul>
                </nav>

                <!-- Usuario -->
                <div class="usuario-header">

                  <details class="usuario-menu">

                      <summary>

                          <img src="<?php echo $URL.'/'.$rutaFoto; ?>" alt="Usuario">

                          <span><?php echo $nombre; ?></span>

                      </summary>

                      <div class="menu-desplegable">

                          <a href="<?php echo $URL; ?>/user/profile">
                              Mi perfil
                          </a>
                          <?php if($cargo=="Administrador"){ ?>
                          <a href="<?php echo $rutaAdmin; ?>">
                              <?php echo $msj; ?>
                          </a>
                          <?php } ?>
                          <hr>

                          <a href="<?php echo $URL; ?>/login/controller_logout.php" class="cerrar">Cerrar sesión</a>
                      </div>
                  </details>
              </div>

            </div>

        </div>
    </header>
    <div id="overlayMenu"></div>

<script>
const btnMenu = document.getElementById("btnMenuMovil");
const menuPrincipal = document.getElementById("menuPrincipal");
const overlayMenu = document.getElementById("overlayMenu");

btnMenu.addEventListener("click", () => {
    menuPrincipal.classList.toggle("menu-abierto");
    overlayMenu.classList.toggle("activo");
});

overlayMenu.addEventListener("click", () => {
    menuPrincipal.classList.remove("menu-abierto");
    overlayMenu.classList.remove("activo");
});

</script>