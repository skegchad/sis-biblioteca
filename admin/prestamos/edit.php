<?php

include("../../app/config/config.php");
include("../../app/config/conexion.php");
include("../../layout/admin/login.php");
include("../../layout/admin/datos_usuario.php");
include("../../layout/admin/comprueba_admin.php");
include("../../layout/admin/parte1.php");

$id_prestamo = $_GET['id'] ?? null;

if (!$id_prestamo) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT
            p.id_prestamo,
            p.fyh_devolucion,
            l.titulo,
            l.autor,
            u.nombre_completo
        FROM prestamos p
        INNER JOIN tb_libros l
            ON l.id_libro = p.id_libro
        INNER JOIN tb_usuarios u
            ON u.id_usuario = p.id_usuario
        WHERE p.id_prestamo = :id_prestamo";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_prestamo',$id_prestamo,PDO::PARAM_INT);
$stmt->execute();

$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prestamo){
    header("Location:index.php");
    exit;
}

?><main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-6">
                    <h3>Editar préstamo</h3>
                </div>
            </div>

            <hr>

            <div class="card">
                <div class="card-body">

                    <form action="controller_edit.php" method="post" id="formEditarPrestamo">

                        <input
                            type="hidden"
                            name="id_prestamo"
                            value="<?= $prestamo['id_prestamo']; ?>">

                        <div class="row">

                            <div class="col-md-6">
                                <label class="form-label">
                                    Libro
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($prestamo['titulo'].' — '.$prestamo['autor']); ?>"
                                    disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Usuario
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($prestamo['nombre_completo']); ?>"
                                    disabled>
                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col-md-4">
                                <label class="form-label">
                                    Fecha de devolución
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    name="fecha_devolucion"
                                    id="fecha_devolucion"
                                    value="<?= date('Y-m-d', strtotime($prestamo['fyh_devolucion'])); ?>"
                                    required>
                            </div>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-2"></div>

                            <div class="col-md-4 d-grid">
                                <a
                                    href="<?php echo $URL; ?>/admin/prestamos"
                                    class="btn btn-default">
                                    Cancelar
                                </a>
                            </div>

                            <div class="col-md-4 d-grid">
                                <button
                                    type="button"
                                    class="btn btn-warning"
                                    onclick="confirmarEdicion()">
                                    Guardar cambios
                                </button>
                            </div>

                            <div class="col-md-2"></div>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</main>

<script>
    function confirmarEdicion(){

    let fecha=document.getElementById("fecha_devolucion").value;

    if(fecha==""){

        Swal.fire(
            "Falta la fecha",
            "Selecciona una fecha de devolución.",
            "warning"
        );

        return;

    }

    Swal.fire({

        title:"¿Guardar cambios?",

        text:"Se actualizará la fecha de devolución.",

        icon:"question",

        showCancelButton:true,

        confirmButtonText:"Guardar",

        cancelButtonText:"Cancelar"

    }).then((result)=>{

        if(result.isConfirmed){

            document.getElementById("formEditarPrestamo").submit();

        }

    });

}
</script>
<?php include("../../layout/admin/parte2.php");?>