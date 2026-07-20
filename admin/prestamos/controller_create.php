<?php

session_start();

require_once("../../app/config/config.php");
require_once("../../app/config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_libro = isset($_POST['id_libro']) ? (int)$_POST['id_libro'] : 0;
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$fecha_devolucion = trim($_POST['fecha_devolucion'] ?? '');

if ($id_libro <= 0 || $id_usuario <= 0 || empty($fecha_devolucion)) {
    $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
    $_SESSION['icono'] = "error";
    header("Location: create.php");
    exit;
}

try {

    $pdo->beginTransaction();

    // Bloquea el registro mientras se realiza el préstamo
    $sqlLibro = "SELECT ejemplares, prestados
                 FROM tb_libros
                 WHERE id_libro = :id_libro
                 FOR UPDATE";

    $stmtLibro = $pdo->prepare($sqlLibro);
    $stmtLibro->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
    $stmtLibro->execute();

    $libro = $stmtLibro->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        throw new Exception("El libro seleccionado no existe.");
    }

    $ejemplares = (int)$libro['ejemplares'];
    $prestados = (int)$libro['prestados'];

    if ($prestados >= $ejemplares) {
        throw new Exception("No hay ejemplares disponibles de este libro.");
    }

    // Registrar préstamo
    $sqlPrestamo = "INSERT INTO prestamos
        (id_libro, id_usuario, fyh_devolucion, estado)
        VALUES
        (:id_libro, :id_usuario, :fyh_devolucion, 'EN CURSO')";

    $stmtPrestamo = $pdo->prepare($sqlPrestamo);

    $stmtPrestamo->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
    $stmtPrestamo->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtPrestamo->bindParam(':fyh_devolucion', $fecha_devolucion);

    $stmtPrestamo->execute();

    // Actualizar libro
    $sqlActualizar = "UPDATE tb_libros
                      SET
                          prestados = prestados + 1,
                          disponibilidad = CASE
                              WHEN (prestados + 1) >= ejemplares THEN 0
                              ELSE 1
                          END,
                          fyh_actualizacion = NOW()
                      WHERE id_libro = :id_libro";

    $stmtActualizar = $pdo->prepare($sqlActualizar);
    $stmtActualizar->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
    $stmtActualizar->execute();

    $pdo->commit();

    $_SESSION['mensaje'] = "El préstamo se registró correctamente.";
    $_SESSION['icono'] = "success";

    header("Location: index.php");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono'] = "error";

    header("Location: create.php");
    exit;
}