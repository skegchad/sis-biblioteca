<?php
// Ubicación esperada: /layout/user/validar_mensaje.php
// (coincide con el action del form en contacto.php, que apunta a esta misma ruta)
 
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../admin/login.php");          // valida que haya sesión activa / setea $cargo
include ("../admin/datos_usuario.php");  // debe dejar disponible el ID del usuario logueado
 
// $id viene seteado desde layout/admin/datos_usuario.php
// (dentro del foreach: $id = $usuario['id_usuario'];)
 
// Si no hay usuario identificado, no dejamos continuar.
if (empty($id)) {
    header("Location: " . $URL . "/user/contacto.php?error=sesion");
    exit;
}
 
// Solo procesamos si vino por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $URL . "/user/contacto.php");
    exit;
}
 
// ---------------------------------------------------------------
// Recolección y limpieza de datos del formulario
// ---------------------------------------------------------------
$email   = trim($_POST['email']   ?? '');
$asunto  = trim($_POST['asunto']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
 
// ---------------------------------------------------------------
// Validaciones básicas
// ---------------------------------------------------------------
if ($email === '' || $asunto === '' || $mensaje === '') {
    header("Location: " . $URL . "/user/contacto.php?error=incompleto");
    exit;
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: " . $URL . "/user/contacto.php?error=email");
    exit;
}
 
// Límite de largo razonable para evitar mensajes gigantes
if (strlen($asunto) > 150) {
    $asunto = substr($asunto, 0, 150);
}
if (strlen($mensaje) > 3000) {
    $mensaje = substr($mensaje, 0, 3000);
}
 
// ---------------------------------------------------------------
// Guardar en base de datos
//
// Tabla esperada (ajusta el nombre/columnas si ya tienes una):
//
// CREATE TABLE mensajes_contacto (
//     id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
//     id_usuario INT NOT NULL,
//     email      VARCHAR(150) NOT NULL,
//     asunto     VARCHAR(150) NOT NULL,
//     mensaje    TEXT NOT NULL,
//     fecha      DATETIME DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
// );
// ---------------------------------------------------------------
try {
    $query = $pdo->prepare(
        'INSERT INTO mensajes (id_usuario, email, asunto, mensaje, fyh_envio)
        VALUES (:id_usuario, :email, :asunto, :mensaje, NOW())'
    );
 
    $query->bindParam(':id_usuario', $id, PDO::PARAM_INT);
    $query->bindParam(':email',      $email);
    $query->bindParam(':asunto',     $asunto);
    $query->bindParam(':mensaje',    $mensaje);
 
    $query->execute();
 
    // -----------------------------------------------------------
    // Si más adelante quieres enviar también por correo, aquí
    // sería el lugar (ej. con PHPMailer):
    //
    // enviarCorreoContacto($email, $asunto, $mensaje);
    // -----------------------------------------------------------
 
    header("Location: " . $URL . "/user/contacto.php?success=mensaje");
    exit;
 
} catch (PDOException $e) {
    // En producción no muestres $e->getMessage() al usuario final.
    // Déjalo solo para depurar en local.
    error_log("Error al guardar mensaje de contacto: " . $e->getMessage());
    header("Location: " . $URL . "/user/contacto.php?error=servidor");
    exit;
}
?>