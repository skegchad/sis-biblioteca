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
    <p>El ancho del lomo = número de páginas del PDF · El color = color dominante de la portada · Click en un libro para verlo de frente</p>
  </div>

  <div class="filtros">
    <select id="f-categoria"></select>
    <select id="f-tema"></select>
    <input id="f-buscar" type="text" placeholder="Buscar por título o autor…">
  </div>

  <div class="estante-wrap">
    <div class="estante" id="estante"></div>
    <div class="tabla"></div>
  </div>

</div>

<script>
  // Le pasamos tu constante $URL de config.php al JS para armar las rutas de la API
  window.APP_URL = "<?= $URL ?>";
</script>

<script>
  window.APP_URL = "<?= $URL ?>";

  console.log("APP_URL:", window.APP_URL);
</script>

<script src="<?= $URL ?>/user/assets/js/estanteria.js"></script>

<?php include("../ai/chat_widget.php"); ?>
