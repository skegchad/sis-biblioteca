<?php
// Ubicación esperada: /admin/mensajes/index.php (o donde tengas el resto del panel admin)
// Sigue el mismo patrón de includes que tu listado de libros.

include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include ("../../layout/admin/comprueba_admin.php");
include ("../../layout/admin/parte1.php");

// ---------------------------------------------------------------
// Trae cada mensaje junto con los datos del usuario que lo envió.
// Ajusta los nombres de columna de tb_usuarios si difieren.
// ---------------------------------------------------------------
$query_mensajes = $pdo->prepare('
    SELECT
        m.id_usuario,
        m.email,
        m.asunto,
        m.mensaje,
        m.fyh_envio,
        u.nombre_completo,
        u.apellidos,
        u.nombre_usuario,
        u.foto
    FROM mensajes m
    LEFT JOIN tb_usuarios u ON m.id_usuario = u.id_usuario
    ORDER BY m.fyh_envio DESC
');
$query_mensajes->execute();
$lista_mensajes = $query_mensajes->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/*
 * No se fijan colores propios (blanco, gris, etc). Se usan las variables
 * de color de Bootstrap 5.3 (--bs-body-bg, --bs-border-color, --bs-secondary-color...).
 * Bootstrap redefine automáticamente esas variables cuando el <html> tiene
 * data-bs-theme="dark", que es justo lo que hace el toggle de AdminLTE.
 * Así las tarjetas cambian solas de claro a oscuro sin JS extra.
 */

.mensajes-grid{
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 10px;
}

.mensaje-card{
    background-color: var(--bs-secondary-bg);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
    border-radius: 12px;
    box-shadow: var(--bs-box-shadow-sm, 0 2px 8px rgba(0,0,0,.1));
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mensaje-card-header{
    display: flex;
    align-items: center;
    gap: 12px;
}

.mensaje-avatar{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid var(--bs-border-color);
}

.mensaje-usuario-nombre{
    font-weight: bold;
    margin: 0;
    line-height: 1.2;
    color: var(--bs-body-color);
}

.mensaje-usuario-email{
    margin: 0;
    font-size: 13px;
    color: var(--bs-secondary-color);
}

.mensaje-asunto{
    font-weight: 600;
    font-size: 15px;
    margin: 0;
    color: var(--bs-emphasis-color, var(--bs-body-color));
}

.mensaje-texto{
    font-size: 14px;
    color: var(--bs-body-color);
    white-space: pre-line;
    max-height: 140px;
    overflow-y: auto;
    margin: 0;
}

.mensaje-fecha{
    font-size: 12px;
    color: var(--bs-secondary-color);
    margin-top: auto;
    text-align: right;
}

.mensajes-vacio{
    text-align: center;
    color: var(--bs-secondary-color);
    padding: 40px 0;
}
</style>

<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Bandeja de mensajes</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mensajes</li>
                    </ol>
                </div>
            </div>
            <hr>
            <!--end::Row-->

            <?php if (empty($lista_mensajes)): ?>
                <p class="mensajes-vacio">No hay mensajes por el momento.</p>
            <?php else: ?>
                <div class="mensajes-grid">
                    <?php foreach ($lista_mensajes as $fila):
                        $foto_usuario = $fila['foto'];
                        if (empty($foto_usuario)) {
                            $foto_usuario = 'public/uploads/img/admin/default.jpg';
                        }

                        $nombre_mostrar = trim(($fila['nombre_completo'] ?? '') . ' ' . ($fila['apellidos'] ?? ''));
                        if ($nombre_mostrar === '') {
                            $nombre_mostrar = $fila['nombre_usuario'] ?? 'Usuario';
                        }

                        $fecha_formateada = $fila['fyh_envio']
                            ? date('d/m/Y H:i', strtotime($fila['fyh_envio']))
                            : '';
                    ?>
                        <div class="mensaje-card">
                            <div class="mensaje-card-header">
                                <img src="<?php echo $URL . '/' . $foto_usuario; ?>" alt="Foto" class="mensaje-avatar">
                                <div>
                                    <p class="mensaje-usuario-nombre"><?php echo htmlspecialchars($nombre_mostrar); ?></p>
                                    <p class="mensaje-usuario-email"><?php echo htmlspecialchars($fila['email']); ?></p>
                                </div>
                            </div>

                            <p class="mensaje-asunto"><?php echo htmlspecialchars($fila['asunto']); ?></p>
                            <p class="mensaje-texto"><?php echo nl2br(htmlspecialchars($fila['mensaje'])); ?></p>

                            <span class="mensaje-fecha"><?php echo $fecha_formateada; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
</main>

<?php include ("../../layout/admin/parte2.php"); ?>