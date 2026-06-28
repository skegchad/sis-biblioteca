<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include("../../layout/admin/parte1.php");?>
    <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Crear Usuarios</h3>
                        </div>
                    </div>
                    <hr>
                    <?php
                            $error = $_GET['error'] ?? null;
                            if ($error === 'contrasena') {
                                echo '<div class="alert alert-danger">¡Las contraseñas no coinciden!</div>';
                            }
                        ?>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header">Llene la información con mucho cuidado</h5>
                        
                        <div class="card-body">
                            <form action="controller_create.php" method="post">    
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" name="Nombres" id="Nombre" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Apellidos" class="form-label">Apellidos</label>
                                        <input type="text" name="Apellidos" id="Apellidos" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Cedula" class="form-label">Cédula</label>
                                        <input type="number" name="Cedula" id="Cedula" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Cargo" class="form-label">Cargo</label>
                                        <select name="Cargo" class="form-select" id="Cargo" onchange="mostrarCurso(this)" required>
                                            <option value="">-- Cargo --</option>
                                            <option value="Administrador">Administrador</option>
                                            <option value="Docente">Docente</option>
                                            <option value="Estudiante">Estudiante</option>
                                            <option value="Público">Público</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="Nombreusuario" class="form-label">Nombre de Usuario</label>
                                        <input type="text" name="Nombreusuario" id="Nombreusuario" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row" id="seccion-curso" style="display:none;">
                                    <div class="col-md-6">
                                        <br>
                                        <label for="Curso" class="form-label">Curso</label>
                                        <select name="Curso" id="Curso" class="form-select" onchange="filtrarParalelo(this)">
                                            <option value="">-- Selecciona --</option>
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
                                            <option value="1ro Bachillerato">1ro Bachillerato</option>
                                            <option value="2do Bachillerato">2do Bachillerato</option>
                                            <option value="3ro Bachillerato">3ro Bachillerato</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <br>
                                        <label for="Paralelo" class="form-label">Paralelo/Especialidad</label>
                                        <select name="Paralelo" id="Paralelo" class="form-select">
                                            <option value="">-- Selecciona --</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                            <option value="INSTALACIONES">INSTALACIONES</option>
                                            <option value="INFORMÁTICA">INFORMÁTICA</option>
                                            <option value="MECANIZADO">MECANIZADO</option>
                                            <option value="ELECTROMECANICA">ELECTROMECANICA</option>
                                            <option value="CONTABILIDAD">CONTABILIDAD</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Password" class="form-label">Contraseña</label>
                                        <input type="password" name="Password" id="Password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="verifyPassword" class="form-label">Verifica Contraseña</label>
                                        <input type="password" name="verifyPassword" id="verifyPassword" class="form-control" required>
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
                                            <button class="btn btn-primary" type="button" onclick="confirmarRegistro(this)">Registrar Usuario</button>
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
function confirmarRegistro(btn) {
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
    confirmButtonText: 'Sí, registrar',
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

    // Resetea selección
    paralelo.value = '';

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
</script>

<?php include("../../layout/admin/parte2.php");?>