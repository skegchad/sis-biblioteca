<?php

session_start();

require_once("../../app/config/config.php");
require_once("../../app/config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_prestamo = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_prestamo <= 0) {
    $_SESSION['mensaje'] = "Préstamo no válido.";
    $_SESSION['icono'] = "error";
    header("Location: index.php");
    exit;
}

try {

    $pdo->beginTransaction();

    // Bloquea el préstamo mientras se procesa la devolución
    $sqlPrestamo = "SELECT id_libro, id_usuario
                     FROM prestamos
                     WHERE id_prestamo = :id_prestamo
                     FOR UPDATE";

    $stmtPrestamo = $pdo->prepare($sqlPrestamo);
    $stmtPrestamo->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
    $stmtPrestamo->execute();

    $prestamo = $stmtPrestamo->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        throw new Exception("El préstamo seleccionado no existe.");
    }

    $id_libro = (int)$prestamo['id_libro'];

    // Bloquea el registro del libro mientras se realiza la devolución
    $sqlLibro = "SELECT ejemplares, prestados
                 FROM tb_libros
                 WHERE id_libro = :id_libro
                 FOR UPDATE";

    $stmtLibro = $pdo->prepare($sqlLibro);
    $stmtLibro->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
    $stmtLibro->execute();

    $libro = $stmtLibro->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        throw new Exception("El libro asociado a este préstamo no existe.");
    }

    // Actualizar libro: se libera un ejemplar
    $sqlActualizar = "UPDATE tb_libros
                      SET
                          prestados = GREATEST(prestados - 1, 0),
                          disponibilidad = CASE
                              WHEN (prestados) >= ejemplares THEN 0
                              ELSE 1
                          END,
                          fyh_actualizacion = NOW()
                      WHERE id_libro = :id_libro";

    $stmtActualizar = $pdo->prepare($sqlActualizar);
    $stmtActualizar->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
    $stmtActualizar->execute();

    // Eliminar el préstamo (equivale a marcarlo como devuelto)
    $sqlEliminar = "DELETE FROM prestamos WHERE id_prestamo = :id_prestamo";

    $stmtEliminar = $pdo->prepare($sqlEliminar);
    $stmtEliminar->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
    $stmtEliminar->execute();

    $pdo->commit();

    $_SESSION['mensaje'] = "El libro se devolvió correctamente.";
    $_SESSION['icono'] = "success";

    header("Location: index.php");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono'] = "error";

    header("Location: index.php");
    exit;
}