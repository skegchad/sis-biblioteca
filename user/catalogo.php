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

<iframe
    src="<?php echo $URL; ?>/layout/user/catalogo.php"
    style="width:100%; height:100vh; border:none;"
    loading="lazy">
</iframe>

<?php include("../ai/chat_widget.php"); ?>
