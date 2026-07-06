<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $URL . "/admin");
    exit;
}

// ---------- 1. Recoger los campos de texto ----------
$titulo         = trim($_POST['titulo'] ?? '');
$autor = trim($_POST['autor'] ?? '');
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
$temasTexto     = trim($_POST['temas'] ?? ''); // ej: "Romance,Aventura"

// Validación básica de campos obligatorios
if (!$autor|| !$titulo || !$categoria || !$subcategoria || !$tipo) {
    die("Faltan campos obligatorios.");
}

// ---------- 2. Subir la foto (portada) ----------
$rutaFoto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $carpetaFoto = __DIR__ . "/../../public/uploads/img/libros/";
    if (!is_dir($carpetaFoto)) {
        mkdir($carpetaFoto, 0755, true);
    }

    $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $nombreArchivo = uniqid('libro_') . '.' . $extension;
    $rutaDestino = $carpetaFoto . $nombreArchivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        $rutaFoto = "public/uploads/img/libros/" . $nombreArchivo;
    }
}

// ---------- 3. Subir el PDF ----------
$rutaPdf = null;

if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    $carpetaPdf = __DIR__ . "/../../public/uploads/pdf/libros/";
    if (!is_dir($carpetaPdf)) {
        mkdir($carpetaPdf, 0755, true);
    }

    $extension = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));

    if ($extension === 'pdf') {
        $nombreArchivo = uniqid('libro_') . '.' . $extension;
        $rutaDestino = $carpetaPdf . $nombreArchivo;

        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $rutaDestino)) {
            $rutaPdf = "public/uploads/pdf/libros/" . $nombreArchivo;
        }
    }
}

// ---------- 4. Insertar el libro ----------
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO tb_libros 
        (titulo, autor, descripcion, idioma, disponibilidad, tipo, edicion, ano, cdd, bloque, categoria, subcategoria, seccion, editorial, ejemplares, prestados, ruta_pdf, ruta_foto, fyh_creacion, estado)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)
    ");

    $stmt->execute([
        $titulo, $autor, $descripcion, $idioma, $disponibilidad, $tipo, $edicion, $ano, $cdd, $bloque,
        $categoria, $subcategoria, $seccion, $editorial, $ejemplares, $prestados,
        $rutaPdf, $rutaFoto
    ]);


    $id_libro = $pdo->lastInsertId();

    // ---------- 5. Guardar los temas en la tabla intermedia ----------
    if ($temasTexto) {
        $nombresTemas = array_filter(array_map('trim', explode(',', $temasTexto)));

        // Buscamos el id del tipo seleccionado (los temas están ligados a un tipo)
        $stmtTipo = $pdo->prepare("SELECT id FROM tipos WHERE nombre = ?");
        $stmtTipo->execute([$tipo]);
        $tipoRow = $stmtTipo->fetch(PDO::FETCH_ASSOC);

        if ($tipoRow) {
            $tipo_id = $tipoRow['id'];

            $stmtTema = $pdo->prepare("SELECT id FROM temas WHERE tipo_id = ? AND nombre = ?");
            $stmtInsertRel = $pdo->prepare("INSERT INTO libro_tema (id_libro, tema_id) VALUES (?, ?)");

            foreach ($nombresTemas as $nombreTema) {
                $stmtTema->execute([$tipo_id, $nombreTema]);
                $temaRow = $stmtTema->fetch(PDO::FETCH_ASSOC);

                if ($temaRow) {
                    $stmtInsertRel->execute([$id_libro, $temaRow['id']]);
                }
            }
        }
        
    }


    $pdo->commit();
    if($ejemplares<$prestados){
        $disponibilidad=0;
    }else{
        $disponibilidad=1;
    }
    header("Location: " . $URL . "/admin/libros/?success=registrado");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: " . $URL . "/admin/libros/create.php?error=bd");
    die;
}