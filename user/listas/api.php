<?php
// =====================================================
// CONFIGURA SOLO ESTAS LÍNEAS (los nombres de TU base de datos)
// =====================================================
const TABLA_LIBROS  = 'tb_libros';      // nombre de tu tabla de libros
const COL_ID_LIBRO  = 'id_libro';          // columna ID de esa tabla
const COL_TITULO    = 'titulo';      // columna del título
const COL_PORTADA   = 'ruta_foto';     // columna de la imagen de portada

include("../../app/config/config.php");
include("../../app/config/conexion.php");

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// -----------------------------------------------------
// Utilidades
// -----------------------------------------------------
function responder($ok, $datos = [], $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(array_merge(['ok' => $ok], $datos), JSON_UNESCAPED_UNICODE);
    exit;
}

// Usuario en sesión (AJUSTA ESTA LÍNEA, ver Paso 3)
$usuario_id = null;
if (!empty($_SESSION['sesion_user'])) {
    $st = $pdo->prepare("SELECT id_usuario FROM tb_usuarios WHERE nombre_usuario = ? AND estado = '1' LIMIT 1");
    $st->execute([$_SESSION['sesion_user']]);
    $usuario_id = $st->fetchColumn() ?: null;
}

if (!$usuario_id) {
    responder(false, ['mensaje' => 'Debes iniciar sesión'], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, ['mensaje' => 'Método no permitido'], 405);
}

$predeterminadas = [
    'leyendo'     => 'Leyendo',
    'quiero_leer' => 'Quiero leer',
    'leido'       => 'Ya leído',
    'favoritos'   => 'Favoritos',
];
$estados = ['leyendo', 'quiero_leer', 'leido']; // los que son excluyentes

// Crea las 4 listas predeterminadas si el usuario aún no las tiene
function asegurar_predeterminadas($pdo, $uid, $predeterminadas) {
    $st = $pdo->prepare("SELECT tipo FROM listas_usuario WHERE usuario_id = ? AND tipo <> 'personalizada'");
    $st->execute([$uid]);
    $existentes = $st->fetchAll(PDO::FETCH_COLUMN);

    $ins = $pdo->prepare("INSERT INTO listas_usuario (usuario_id, nombre, tipo) VALUES (?, ?, ?)");
    foreach ($predeterminadas as $tipo => $nombre) {
        if (!in_array($tipo, $existentes)) $ins->execute([$uid, $nombre, $tipo]);
    }
}

// Devuelve la lista solo si pertenece al usuario (seguridad)
function lista_del_usuario($pdo, $lista_id, $uid) {
    $st = $pdo->prepare("SELECT * FROM listas_usuario WHERE id = ? AND usuario_id = ?");
    $st->execute([$lista_id, $uid]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function id_lista_por_tipo($pdo, $uid, $tipo) {
    $st = $pdo->prepare("SELECT id FROM listas_usuario WHERE usuario_id = ? AND tipo = ?");
    $st->execute([$uid, $tipo]);
    return $st->fetchColumn();
}

function validar_nombre($nombre, $predeterminadas) {
    $nombre = trim($nombre);
    if ($nombre === '') responder(false, ['mensaje' => 'El nombre no puede estar vacío'], 400);
    if (mb_strlen($nombre) > 40) responder(false, ['mensaje' => 'Máximo 40 caracteres'], 400);
    foreach ($predeterminadas as $n) {
        if (mb_strtolower($n) === mb_strtolower($nombre)) {
            responder(false, ['mensaje' => 'Ese nombre está reservado'], 400);
        }
    }
    return $nombre;
}

// Mueve el libro a UN estado (quita los otros dos estados)
function establecer_estado($pdo, $uid, $libro_id, $estado, $estados) {
    // 1) Quitar el libro de leyendo / quiero_leer / leido
    $marcas = implode(',', array_fill(0, count($estados), '?'));
    $del = $pdo->prepare("
        DELETE ll FROM lista_libros ll
        JOIN listas_usuario lu ON lu.id = ll.lista_id
        WHERE lu.usuario_id = ? AND ll.libro_id = ? AND lu.tipo IN ($marcas)
    ");
    $del->execute(array_merge([$uid, $libro_id], $estados));

    // 2) Agregarlo al nuevo estado (si no es "ninguno")
    if ($estado !== 'ninguno') {
        $lista_id = id_lista_por_tipo($pdo, $uid, $estado);
        $ins = $pdo->prepare("INSERT IGNORE INTO lista_libros (lista_id, libro_id) VALUES (?, ?)");
        $ins->execute([$lista_id, $libro_id]);
    }
}

// -----------------------------------------------------
// Router: según "accion" hace una cosa u otra
// -----------------------------------------------------
asegurar_predeterminadas($pdo, $usuario_id, $predeterminadas);

$accion   = $_POST['accion'] ?? '';
$libro_id = isset($_POST['libro_id']) ? (int)$_POST['libro_id'] : 0;
$lista_id = isset($_POST['lista_id']) ? (int)$_POST['lista_id'] : 0;

try {
    switch ($accion) {

        // ---------- Ver todas mis listas con sus libros ----------
        case 'obtener_listas':
            $st = $pdo->prepare("SELECT id, nombre, tipo FROM listas_usuario WHERE usuario_id = ? ORDER BY FIELD(tipo,'leyendo','quiero_leer','leido','favoritos','personalizada'), nombre");
            $st->execute([$usuario_id]);
            $listas = $st->fetchAll(PDO::FETCH_ASSOC);

            $sqlLibros = "SELECT l." . COL_ID_LIBRO . " AS id, l." . COL_TITULO . " AS titulo, l." . COL_PORTADA . " AS portada
                          FROM lista_libros ll
                          JOIN " . TABLA_LIBROS . " l ON l." . COL_ID_LIBRO . " = ll.libro_id
                          WHERE ll.lista_id = ?
                          ORDER BY ll.fecha_agregado DESC";
            $stLibros = $pdo->prepare($sqlLibros);

            foreach ($listas as &$lista) {
                $stLibros->execute([$lista['id']]);
                $lista['libros'] = $stLibros->fetchAll(PDO::FETCH_ASSOC);
            }
            responder(true, ['listas' => $listas]);
            break;

        // ---------- Crear lista personalizada ----------
        case 'crear_lista':
            $nombre = validar_nombre($_POST['nombre'] ?? '', $predeterminadas);
            $st = $pdo->prepare("INSERT INTO listas_usuario (usuario_id, nombre, tipo) VALUES (?, ?, 'personalizada')");
            $st->execute([$usuario_id, $nombre]);
            responder(true, ['mensaje' => 'Lista creada', 'id' => $pdo->lastInsertId()]);
            break;

        // ---------- Renombrar (solo personalizadas) ----------
        case 'renombrar_lista':
            $lista = lista_del_usuario($pdo, $lista_id, $usuario_id);
            if (!$lista || $lista['tipo'] !== 'personalizada') responder(false, ['mensaje' => 'No permitido'], 403);
            $nombre = validar_nombre($_POST['nombre'] ?? '', $predeterminadas);
            $st = $pdo->prepare("UPDATE listas_usuario SET nombre = ? WHERE id = ? AND usuario_id = ?");
            $st->execute([$nombre, $lista_id, $usuario_id]);
            responder(true, ['mensaje' => 'Lista renombrada']);
            break;

        // ---------- Eliminar (solo personalizadas) ----------
        case 'eliminar_lista':
            $lista = lista_del_usuario($pdo, $lista_id, $usuario_id);
            if (!$lista || $lista['tipo'] !== 'personalizada') responder(false, ['mensaje' => 'No permitido'], 403);
            $st = $pdo->prepare("DELETE FROM listas_usuario WHERE id = ? AND usuario_id = ?");
            $st->execute([$lista_id, $usuario_id]);
            responder(true, ['mensaje' => 'Lista eliminada']);
            break;

        // ---------- En qué listas está este libro (para marcar los checks) ----------
        case 'listas_de_libro':
            if (!$libro_id) responder(false, ['mensaje' => 'Falta el libro'], 400);

            $st = $pdo->prepare("
                SELECT lu.id, lu.nombre, lu.tipo,
                       (SELECT COUNT(*) FROM lista_libros ll WHERE ll.lista_id = lu.id AND ll.libro_id = ?) AS contiene
                FROM listas_usuario lu
                WHERE lu.usuario_id = ?
                ORDER BY FIELD(lu.tipo,'leyendo','quiero_leer','leido','favoritos','personalizada'), lu.nombre
            ");
            $st->execute([$libro_id, $usuario_id]);
            $listas = $st->fetchAll(PDO::FETCH_ASSOC);

            // Estado actual (leyendo / quiero_leer / leido / ninguno) y si es favorito
            $estado = 'ninguno'; $favorito = false;
            foreach ($listas as &$l) {
                $l['contiene'] = (bool)$l['contiene'];
                if ($l['contiene'] && in_array($l['tipo'], $estados)) $estado = $l['tipo'];
                if ($l['contiene'] && $l['tipo'] === 'favoritos') $favorito = true;
            }
            responder(true, ['listas' => $listas, 'estado' => $estado, 'favorito' => $favorito]);
            break;

        // ---------- Agregar libro a una lista ----------
        case 'agregar_a_lista':
            $lista = lista_del_usuario($pdo, $lista_id, $usuario_id);
            if (!$lista || !$libro_id) responder(false, ['mensaje' => 'Datos inválidos'], 400);

            if (in_array($lista['tipo'], $estados)) {
                establecer_estado($pdo, $usuario_id, $libro_id, $lista['tipo'], $estados);
            } else {
                $st = $pdo->prepare("INSERT IGNORE INTO lista_libros (lista_id, libro_id) VALUES (?, ?)");
                $st->execute([$lista_id, $libro_id]);
            }
            responder(true, ['mensaje' => 'Agregado a ' . $lista['nombre']]);
            break;

        // ---------- Quitar libro de una lista ----------
        case 'quitar_de_lista':
            $lista = lista_del_usuario($pdo, $lista_id, $usuario_id);
            if (!$lista || !$libro_id) responder(false, ['mensaje' => 'Datos inválidos'], 400);
            $st = $pdo->prepare("DELETE FROM lista_libros WHERE lista_id = ? AND libro_id = ?");
            $st->execute([$lista_id, $libro_id]);
            responder(true, ['mensaje' => 'Quitado de ' . $lista['nombre']]);
            break;

        // ---------- Cambiar estado: leyendo / quiero_leer / leido / ninguno ----------
        case 'cambiar_estado':
            $estado = $_POST['estado'] ?? '';
            if (!$libro_id || !in_array($estado, array_merge($estados, ['ninguno']))) {
                responder(false, ['mensaje' => 'Datos inválidos'], 400);
            }
            establecer_estado($pdo, $usuario_id, $libro_id, $estado, $estados);
            responder(true, ['mensaje' => 'Estado actualizado']);
            break;

        // ---------- Favorito: si está lo quita, si no está lo pone ----------
        case 'toggle_favorito':
            if (!$libro_id) responder(false, ['mensaje' => 'Falta el libro'], 400);
            $fav_id = id_lista_por_tipo($pdo, $usuario_id, 'favoritos');

            $st = $pdo->prepare("SELECT COUNT(*) FROM lista_libros WHERE lista_id = ? AND libro_id = ?");
            $st->execute([$fav_id, $libro_id]);

            if ($st->fetchColumn() > 0) {
                $pdo->prepare("DELETE FROM lista_libros WHERE lista_id = ? AND libro_id = ?")->execute([$fav_id, $libro_id]);
                responder(true, ['favorito' => false, 'mensaje' => 'Quitado de favoritos']);
            } else {
                $pdo->prepare("INSERT INTO lista_libros (lista_id, libro_id) VALUES (?, ?)")->execute([$fav_id, $libro_id]);
                responder(true, ['favorito' => true, 'mensaje' => 'Agregado a favoritos']);
            }
            break;

        default:
            responder(false, ['mensaje' => 'Acción no válida'], 400);
    }

} catch (PDOException $e) {
    // 23000 = duplicado (por ejemplo, nombre de lista repetido)
    if ($e->getCode() == 23000) {
        responder(false, ['mensaje' => 'Ya tienes una lista con ese nombre'], 409);
    }
    responder(false, ['mensaje' => 'Error en el servidor'], 500);
}