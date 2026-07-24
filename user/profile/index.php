<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");

if($cargo=="Administrador"){
    $msj="Ir a página de administrador";
    $rutaAdmin= $URL."/admin";
}else{
    $msj="Cerrar Sesión";
    $rutaAdmin= $URL."/login/controller_logout.php";
}

// Estas variables ya vienen resueltas por datos_usuario.php:
// $id, $nombre, $apellidos, $nombreusuario, $cedula, $cargo, $curso, $paralelo, $rutaFoto

$foto_perfil = $URL . "/" . ltrim($rutaFoto, '/');

// Mostrar curso/paralelo solo si tienen datos (típicamente estudiantes)
$mostrarCursoParalelo = !empty($curso) || !empty($paralelo);

include ("../../layout/user/part1.php");
?>

<style>
.perfil-wrapper {
    max-width: 1000px;
    margin: 50px auto;
    display: flex;
    gap: 30px;
    align-items: flex-start;
    padding: 0 20px;
}

.perfil-tabla-contenedor {
    flex: 1;
    border: 1px solid #d0dce8;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.perfil-tabla {
    width: 100%;
    border-collapse: collapse;
}

.perfil-tabla tr:nth-child(even) {
    background-color: #f4f8fc;
}

.perfil-tabla tr:nth-child(odd) {
    background-color: #ffffff;
}

.perfil-tabla td {
    padding: 14px 18px;
    border-bottom: 1px solid #e6edf3;
    font-size: 14px;
}

.perfil-tabla tr:last-child td {
    border-bottom: none;
}

.perfil-label {
    color: #2f6fb3;
    font-weight: 600;
    width: 220px;
}

.perfil-valor {
    color: #333;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.perfil-editar-icon {
    color: #2f6fb3;
    cursor: pointer;
    font-size: 14px;
}

.perfil-editar-icon:hover {
    color: #1d4f85;
}

.perfil-foto-box {
    width: 160px;
    flex-shrink: 0;
    text-align: center;
}

.perfil-foto-wrap {
    position: relative;
    width: 160px;
    height: 160px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #d0dce8;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    cursor: pointer;
}

.perfil-foto-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.perfil-foto-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    opacity: 0;
    transition: opacity .2s;
    text-align: center;
    padding: 10px;
}

.perfil-foto-wrap:hover .perfil-foto-overlay {
    opacity: 1;
}

.perfil-btn-password {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 16px;
    background: #0d6efd;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: background .2s;
}

.perfil-btn-password:hover {
    background: #0b5ed7;
    color: #fff;
}

@media (max-width: 768px) {
    .perfil-wrapper {
        flex-direction: column-reverse;
        align-items: center;
    }
}
</style>

<div class="perfil-wrapper">

    <div class="perfil-tabla-contenedor">
        <table class="perfil-tabla">
            <tr>
                <td class="perfil-label">Cédula / Pasaporte</td>
                <td class="perfil-valor"><?= htmlspecialchars($cedula); ?></td>
            </tr>
            <tr>
                <td class="perfil-label">Nombres</td>
                <td class="perfil-valor"><?= htmlspecialchars($nombre); ?></td>
            </tr>
            <tr>
                <td class="perfil-label">Apellidos</td>
                <td class="perfil-valor"><?= htmlspecialchars($apellidos); ?></td>
            </tr>
            <tr>
                <td class="perfil-label">Usuario</td>
                <td class="perfil-valor">
                    <span id="valorUsuario"><?= htmlspecialchars($nombreusuario); ?></span>
                    <i class="icon-edit perfil-editar-icon" id="btnEditarUsuario" title="Cambiar usuario">✎</i>
                </td>
            </tr>
            <?php if ($mostrarCursoParalelo): ?>
            <tr>
                <td class="perfil-label">Curso</td>
                <td class="perfil-valor"><?= htmlspecialchars($curso); ?></td>
            </tr>
            <tr>
                <td class="perfil-label">Paralelo</td>
                <td class="perfil-valor"><?= htmlspecialchars($paralelo); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="perfil-label">Contraseña</td>
                <td class="perfil-valor">
                    ••••••••
                    <a href="<?= $URL; ?>/login/change_password/index.php" class="perfil-btn-password">
                        Cambiar
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div class="perfil-foto-box">
        <form id="fotoPerfilForm"
              action="<?= $URL; ?>/user/profile/controller_update_foto.php"
              method="POST"
              enctype="multipart/form-data">
            <div class="perfil-foto-wrap" id="fotoPerfilTrigger">
                <img src="<?= $foto_perfil; ?>" alt="Foto de perfil" id="fotoPerfilPreview">
                <div class="perfil-foto-overlay">Click para cambiar foto</div>
                
            </div>
            <span>Cambiar foto</span>
            <input type="file" name="foto" id="fotoPerfilInput" accept="image/*" hidden>
        </form>
        
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ===== Cambiar foto de perfil =====
    const fotoTrigger = document.getElementById("fotoPerfilTrigger");
    const fotoInput = document.getElementById("fotoPerfilInput");
    const fotoForm = document.getElementById("fotoPerfilForm");

    fotoTrigger.addEventListener("click", function () {
        fotoInput.click();
    });

    fotoInput.addEventListener("change", function () {
        if (this.files.length > 0) {
            Swal.fire({
                title: '¿Actualizar foto de perfil?',
                text: 'Se reemplazará tu foto actual.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fotoForm.submit();
                } else {
                    fotoInput.value = "";
                }
            });
        }
    });

    // ===== Cambiar nombre de usuario =====
    document.getElementById("btnEditarUsuario").addEventListener("click", function () {

        const usuarioActual = document.getElementById("valorUsuario").textContent.trim();

        Swal.fire({
            title: 'Cambiar nombre de usuario',
            input: 'text',
            inputValue: usuarioActual,
            inputPlaceholder: 'Nuevo nombre de usuario',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value || value.trim().length < 3) {
                    return 'El usuario debe tener al menos 3 caracteres';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {

                fetch("<?= $URL; ?>/user/profile/controller_update_usuario.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "nombre_usuario=" + encodeURIComponent(result.value.trim())
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Listo!', 'Usuario actualizado correctamente.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo actualizar.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
                });
            }
        });
    });

});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>