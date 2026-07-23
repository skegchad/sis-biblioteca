<?php
include("../app/config/config.php");
include("../layout/admin/login.php");
include("../layout/admin/comprueba_admin.php");
?>

<form id="uploadForm"
      action="<?= $URL ?>/admin/noticias_controller_create.php"
      method="POST"
      enctype="multipart/form-data">

    <input type="file"
           id="imagen"
           name="imagen"
           accept="image/*"
           hidden>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
Swal.fire({
    title: 'Agregar imagen',
    html: `
        Se recomienda utilizar imágenes con una relación de aspecto
        <b>4:1</b> para que se visualicen correctamente en el carrusel.
    `,
    icon: 'info',
    confirmButtonText: 'Seleccionar imagen',
    allowOutsideClick: false
}).then(() => {

    const input = document.getElementById("imagen");

    input.click();

    input.onchange = function () {
        if (this.files.length) {
            document.getElementById("uploadForm").submit();
        } else {
            history.back();
        }
    };

});
</script>