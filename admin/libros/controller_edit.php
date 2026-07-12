<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $URL . "/admin");
    exit;
}


// ---------- 1. Recoger los campos de texto ----------
$id_libro = trim($_GET['id']);

$titulo         = trim($_POST['titulo'] ?? '');
$autor          = trim($_POST['autor'] ?? '');
$descripcion    = trim($_POST['descripcion'] ?? '');
$idioma         = trim($_POST['idioma'] ?? '');
$disponibilidad = trim($_POST['disponibilidad'] ?? '');
$edicion        = trim($_POST['edicion'] ?? '');
$ano            = trim($_POST['ano'] ?? '');
$cdd            = trim($_POST['cdd'] ?? '');
$bloque         = trim($_POST['bloque'] ?? '');
$categoria      = trim($_POST['categoria'] ?? '');
$subcategoria   = trim($_POST['subcategoria'] ?? '');
$seccion        = trim($_POST['seccion'] ?? '');
$editorial      = trim($_POST['editorial'] ?? '');
$ejemplares     = trim($_POST['ejemplares'] ?? 0);
$prestados      = trim($_POST['prestados'] ?? 0);
$tipo           = trim($_POST['tipo'] ?? '');
$temasTexto     = trim($_POST['temas'] ?? '');


// Validación básica
if (!$autor || !$titulo || !$categoria || !$subcategoria || !$tipo) {
    die("Faltan campos obligatorios.");
}


// ---------- 2. Obtener archivos actuales ----------
$stmtActual = $pdo->prepare(
    "SELECT ruta_pdf, ruta_foto FROM tb_libros WHERE id_libro = ?"
);

$stmtActual->execute([$id_libro]);

$archivoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);


$rutaFoto = $archivoActual['ruta_foto'];
$rutaPdf  = $archivoActual['ruta_pdf'];



// ---------- 3. Subir nueva foto si existe ----------
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $carpetaFoto = __DIR__ . "/../../public/uploads/img/libros/";

    if (!is_dir($carpetaFoto)) {
        mkdir($carpetaFoto, 0755, true);
    }


    $extension = strtolower(
        pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
    );

    $nombreArchivo = uniqid('libro_') . '.' . $extension;

    $rutaDestino = $carpetaFoto . $nombreArchivo;


    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {

        $rutaFoto = "public/uploads/img/libros/" . $nombreArchivo;

    }
}



// ---------- 4. Subir nuevo PDF si existe ----------
if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {


    $carpetaPdf = __DIR__ . "/../../public/uploads/pdf/libros/";


    if (!is_dir($carpetaPdf)) {
        mkdir($carpetaPdf, 0755, true);
    }


    $extension = strtolower(
        pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)
    );


    if ($extension === 'pdf') {


        $nombreArchivo = uniqid('libro_') . '.' . $extension;

        $rutaDestino = $carpetaPdf . $nombreArchivo;


        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $rutaDestino)) {

            $rutaPdf = "public/uploads/pdf/libros/" . $nombreArchivo;

        }

    }

}



// ---------- 5. Editar libro ----------
try {

    $pdo->beginTransaction();


    // Actualizar disponibilidad antes del UPDATE
    if ($ejemplares < $prestados) {
        $disponibilidad = 0;
    } else {
        $disponibilidad = 1;
    }



    $stmt = $pdo->prepare("UPDATE tb_libros SET
        titulo         = :titulo,
        autor          = :autor,
        descripcion    = :descripcion,
        idioma         = :idioma,
        disponibilidad = :disponibilidad,
        tipo           = :tipo,
        edicion        = :edicion,
        ano            = :ano,
        cdd            = :cdd,
        bloque         = :bloque,
        categoria      = :categoria,
        subcategoria   = :subcategoria,
        seccion        = :seccion,
        editorial      = :editorial,
        ejemplares     = :ejemplares,
        prestados      = :prestados,
        ruta_pdf       = :ruta_pdf,
        ruta_foto      = :ruta_foto,
        fyh_actualizacion =:fyh_actualizacion

        WHERE id_libro = :id_libro
    ");



    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':idioma', $idioma);

    $stmt->bindParam(':disponibilidad', $disponibilidad);
    $stmt->bindParam(':tipo', $tipo);

    $stmt->bindParam(':edicion', $edicion);
    $stmt->bindParam(':ano', $ano);
    $stmt->bindParam(':cdd', $cdd);
    $stmt->bindParam(':bloque', $bloque);

    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':subcategoria', $subcategoria);
    $stmt->bindParam(':seccion', $seccion);

    $stmt->bindParam(':editorial', $editorial);

    $stmt->bindParam(':ejemplares', $ejemplares);
    $stmt->bindParam(':prestados', $prestados);

    $stmt->bindParam(':ruta_pdf', $rutaPdf);
    $stmt->bindParam(':ruta_foto', $rutaFoto);
    $stmt->bindParam(':fyh_actualizacion', $fyh_actual);

    $stmt->bindParam(':id_libro', $id_libro);



    $stmt->execute();

    // ---------- 6. Actualizar temas del libro ----------
    // Borrar relaciones anteriores
    $stmtDeleteTemas = $pdo->prepare(
        "DELETE FROM libro_tema WHERE id_libro = ?"
    );
    $stmtDeleteTemas->execute([$id_libro]);

    // Guardar nombres para tb_libros.temas
    $temasGuardados = [];

    // Si existen temas seleccionados
    if (!empty($temasTexto)) {

        $nombresTemas = array_filter(
            array_map('trim', explode(',', $temasTexto))
        );



        // Buscar el id del tipo
        $stmtTipo = $pdo->prepare(
            "SELECT id FROM tipos WHERE nombre = ?"
        );

        $stmtTipo->execute([$tipo]);


        $tipoRow = $stmtTipo->fetch(PDO::FETCH_ASSOC);



        if ($tipoRow) {


            $tipo_id = $tipoRow['id'];



            $stmtTema = $pdo->prepare(
                "SELECT id 
                 FROM temas 
                 WHERE tipo_id = ? 
                 AND nombre = ?"
            );



            $stmtInsertTema = $pdo->prepare(
                "INSERT INTO libro_tema 
                (id_libro, tema_id)
                VALUES (?, ?)"
            );



            foreach ($nombresTemas as $nombreTema) {


                $stmtTema->execute([
                    $tipo_id,
                    $nombreTema
                ]);



                $temaRow = $stmtTema->fetch(PDO::FETCH_ASSOC);



                if ($temaRow) {


                    // Crear relación
                    $stmtInsertTema->execute([
                        $id_libro,
                        $temaRow['id']
                    ]);



                    // Guardar nombre
                    $temasGuardados[] = $nombreTema;

                }

            }

        }

    }



    // Actualizar campo texto de tb_libros.temas
    // Si no hay temas queda vacío
    $temasTextoFinal = implode(", ", $temasGuardados);



    $stmtUpdateTemas = $pdo->prepare(
        "UPDATE tb_libros 
         SET temas = ?
         WHERE id_libro = ?"
    );


    $stmtUpdateTemas->execute([
        $temasTextoFinal,
        $id_libro
    ]);





    // Confirmar cambios
    $pdo->commit();



    header(
        "Location: " . $URL . "/admin/libros/?success=actualizado"
    );

    exit;



} catch (Exception $e) {


    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    header(
        "Location: " . $URL . "/admin/libros/edit.php?error=bd"
    );

    exit;

}