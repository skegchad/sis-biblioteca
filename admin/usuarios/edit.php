
<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include("../../layout/admin/parte1.php");
$id_get=$_GET['id'];
$query = $pdo->prepare("SELECT * FROM tb_usuarios WHERE id_usuario='$id_get' ");
$query->execute();
$usuarios=$query->fetchAll(PDO::FETCH_ASSOC);
foreach($usuarios as $usuario){
    $nombre = $usuario['nombre_completo'];
    $apellidos = $usuario['apellidos'];
    $nombreusuario = $usuario['nombre_usuario'];
    $cedula = $usuario['cedula'];
    $cargo = $usuario['cargo'];
    $curso = $usuario['curso'];
    $paralelo = $usuario['paralelo'];
    $foto = $usuario['foto'];
}?>
    <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Editar Usuario</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header" style="background-color: #00a716; color: white">Llene la información con mucho cuidado</h5>
                        
                        <div class="card-body">
                            <form action="controller_edit.php?id=<?php echo $id_get;?>" method="post" enctype="multipart/form-data">    
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" value="<?php echo $nombre;?>" name="Nombres" id="Nombre" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Apellidos" class="form-label">Apellidos</label>
                                        <input type="text" value="<?php echo $apellidos;?>" name="Apellidos" id="Apellidos" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Cedula" class="form-label">Cédula</label>
                                        <input type="number" value="<?php echo $cedula;?>" name="Cedula" id="Cedula" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Cargo" class="form-label">Cargo</label>
                                        <select name="Cargo" class="form-select" id="Cargo" onchange="mostrarCurso(this)" required>
                                            <option value="">-- Cargo --</option>
                                            <option value="Administrador" <?php echo $cargo === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                                            <option value="Docente"       <?php echo $cargo === 'Docente'       ? 'selected' : ''; ?>>Docente</option>
                                            <option value="Estudiante"    <?php echo $cargo === 'Estudiante'    ? 'selected' : ''; ?>>Estudiante</option>
                                            <option value="Público"       <?php echo $cargo === 'Público'       ? 'selected' : ''; ?>>Público</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="Nombreusuario" class="form-label">Nombre de Usuario</label>
                                        <input type="text" value="<?php echo $nombreusuario;?>" name="Nombreusuario" id="Nombreusuario" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row" id="seccion-curso" style="display:none;">
                                    <div class="col-md-6">
                                        <br>
                                        <label for="Curso" class="form-label">Curso</label>
                                        <select name="Curso" id="Curso" class="form-select" onchange="filtrarParalelo(this)">
                                            <option value="<?php echo $curso;?>"><?php echo $curso;?></option>
                                            <option value="1ro">1ro</option>
                                            <option value="2do">2do</option>
                                            <option value="3ro">3ro</option>
                                            <option value="4to">4to</option>
                                            <option value="5to">5to</option>
                                            <option value="6to">6to</option>
                                            <option value="7mo">7mo</option>
                                            <option value="8vo">8vo</option>
                                            <option value="9no">9no</option>
                                            <option value="10mo">10mo</option>
                                            <option value="1roB">1ro de bachillerato</option>
                                            <option value="2doB">2do de bachillerato</option>
                                            <option value="3roB">3ro de bachillerato</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <br>
                                        <label for="Paralelo" class="form-label">Paralelo/Especialidad</label>
                                        <select name="Paralelo" id="Paralelo" class="form-select">
                                            <option value="<?php echo $paralelo;?>"><?php echo $paralelo;?></option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                            <option value="instalaciones">INSTALACIONES</option>
                                            <option value="informatica">INFORMÁTICA</option>
                                            <option value="mecanizado">MECANIZADO</option>
                                            <option value="electromecanica">ELECTROMECANICA</option>
                                            <option value="contabilidad">CONTABILIDAD</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="mb-3">
                                        <label class="form-label">Foto de perfil (OPCIONAL)</label>
                                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                        
                                        <!-- Preview con la foto actual -->
                                        <img id="preview" src="<?php echo $URL;?>/<?php echo $foto;?>" 
                                            class="mt-2 rounded-circle" width="80" height="80" style="object-fit:cover;">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="confirmarActualizar(this)" type="button">Actualizar Usuario</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <!--end::Container-->
            </div>
    </main>
<script>
document.getElementById('foto').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
    }
});    
function confirmarActualizar(btn) {
  const form = btn.closest('form');

  // Dispara la validación nativa del navegador
  if (!form.checkValidity()) {
    form.reportValidity(); // muestra los mensajes de error
    return;
  }

  Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Esta información es correcta?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, actualizar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#0d6efd',
    cancelButtonColor: '#6c757d',
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}
function mostrarCurso(select) {
    const seccion = document.getElementById('seccion-curso');
    const curso = document.getElementById('Curso');
    const paralelo = document.getElementById('Paralelo');
    const esEstudiante = select.value === 'Estudiante';

    seccion.style.display = esEstudiante ? 'flex' : 'none';

    // Agrega o quita el required según el cargo
    if (esEstudiante) {
        curso.setAttribute('required', 'required');
        paralelo.setAttribute('required', 'required');
    } else {
        curso.removeAttribute('required');
        paralelo.removeAttribute('required');
        curso.value = '';
        paralelo.value = '';
    }
}
function filtrarParalelo(select) {
    const esBachillerato = ['1roB', '2doB', '3roB'].includes(select.value);
    const paralelo = document.getElementById('Paralelo');

    // Muestra u oculta cada opción según el curso
    paralelo.querySelectorAll('option').forEach(option => {
        const esEspecialidad = ['instalaciones', 'informatica', 'mecanizado', 'electromecanica', 'contabilidad'].includes(option.value);
        const esLetra = ['A', 'B', 'C', 'D', 'E'].includes(option.value);

        if (esEspecialidad) {
            option.hidden = !esBachillerato;  // solo bachillerato ve especialidades
        }
        if (esLetra) {
            option.hidden = esBachillerato;   // bachillerato no ve A, B, C, D, E
        }
    });
}
window.onload = function() {
    const cargo = document.getElementById('Cargo');
    if (cargo.value === 'Estudiante') {
        document.getElementById('seccion-curso').style.display = 'flex';
        document.getElementById('Curso').setAttribute('required', 'required');
        document.getElementById('Paralelo').setAttribute('required', 'required');
    }
}
</script>

<?php include("../../layout/admin/parte2.php");?>