<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit;
}

$id_prestamo = $_POST['id_prestamo'] ?? null;
$fecha_devolucion = $_POST['fecha_devolucion'] ?? '';

if (empty($id_prestamo) || empty($fecha_devolucion)) {

    session_start();
    $_SESSION['mensaje'] = "Debe completar todos los campos.";
    $_SESSION['icono'] = "error";

    header("Location: update.php?id=" . $id_prestamo);
    exit;
}

try {

    $sql = "UPDATE prestamos
            SET fyh_devolucion = :fecha_devolucion
            WHERE id_prestamo = :id_prestamo";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':fecha_devolucion', $fecha_devolucion, PDO::PARAM_STR);
    $stmt->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);

    if ($stmt->execute()) {

        session_start();
        $_SESSION['mensaje'] = "Préstamo actualizado correctamente.";
        $_SESSION['icono'] = "success";

    } else {

        session_start();
        $_SESSION['mensaje'] = "Error al actualizar el préstamo.";
        $_SESSION['icono'] = "error";

    }

} catch (PDOException $e) {

    session_start();
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['icono'] = "error";

}

header("Location: index.php");
exit;

?>