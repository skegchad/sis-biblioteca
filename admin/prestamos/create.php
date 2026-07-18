<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include ("../../layout/admin/comprueba_admin.php");
include("../../layout/admin/parte1.php");?>


    <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Nuevo Prestamo</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header"></h5>
                        
                        <div class="card-body">
                            <form action="controller_create.php" method="post">    
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" name="Nombre" id="Nombre" class="form-control" required>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Temas</label>

                                        <!-- Tarjetas de temas agregados -->
                                        <div id="temasBox" class="d-flex flex-wrap gap-2 mb-2 p-2 border rounded" style="min-height: 50px;">
                                            <span class="text-muted small" id="temaPlaceholder">Aún no has agregado temas</span>
                                        </div>

                                        <!-- Input para agregar un tema -->
                                        <div class="input-group">
                                            <input type="text" id="temaInput" class="form-control" placeholder="Nuevo tema...">
                                            <button type="button" class="btn btn-outline-secondary" id="btnAgregarTema" onclick="agregarTema()">+ Agregar</button>
                                        </div>

                                        <!-- Nombres de los temas, para el submit -->
                                        <input type="hidden" name="temas" id="temasHidden">
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin/libros/tipos" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="button" onclick="confirmarRegistro(this)">Registrar Tipo</button>
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

<?php include("../../layout/admin/parte2.php"); ?>