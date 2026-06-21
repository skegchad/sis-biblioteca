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

                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header">Llene la información con mucho cuidado</h5>
                        <div class="card-body">
                            <form action="controller_create.php" method="post">    
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" id="Nombre" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Apellidos" class="form-label">Apellidos</label>
                                        <input type="text" id="Apellidos" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="Cedula" class="form-label">Cédula</label>
                                        <input type="number" id="Cedula" class="form-control">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Cargo" class="form-label">Cargo</label>
                                        <select name="Cargo" class="form-select" id="Cargo" onchange="mostrarCurso(this)">
                                            <option value="">-- Cargo --</option>
                                            <option value="Administrador">Administrador</option>
                                            <option value="Docente">Docente</option>
                                            <option value="Estudiante">Estudiante</option>
                                            <option value="Público">Público</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="Nombreusuario" class="form-label">Nombre de Usuario</label>
                                        <input type="text" id="Nombreusuario" class="form-control">
                                    </div>
                                </div>
                                <div class="row" id="seccion-curso" style="display:none;">
                                        <div class="col-md-6">
                                            <label for="Curso" class="form-label">Curso:</label>
                                            <select name="Curso" id="Curso" class="form-select">
                                                <option value="">-- Selecciona --</option>
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
                                            <label for="Paralelo" class="form-label">Paralelo/Especialidad:</label>
                                            <select name="Paralelo" id="Paralelo" class="form-select">
                                                <option value="">-- Selecciona --</option>
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
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Password" class="form-label">Contraseña</label>
                                        <input type="password" id="Password" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="verifyPassword" class="form-label">Verifica Contraseña</label>
                                        <input type="password" id="verifyPassword" class="form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <!--end::Container-->
            </div>
    </main>
<script>
function mostrarCurso(select) {
    const seccion = document.getElementById('seccion-curso');
    seccion.style.display = select.value === 'Estudiante' ? 'block' : 'none';

    // Limpia el valor si cambian de cargo
    if (select.value !== 'Estudiante') {
        document.getElementById('Curso').value = '';
    }
}
</script>

<?php include("../../layout/admin/parte2.php");?>