<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");
include ("../layout/admin/login.php");
include ("../layout/admin/datos_usuario.php");
include("../layout/admin/parte1.php");?>

      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0">INFORMACION DEL USUARIO</h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
              <hr>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>

        <!--end::App Content Header-->
          <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <table class="table table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Nombres</th>
                      <th scope="col"><?php echo $nombre; ?></th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    <tr>
                      <th scope="row">Apellidos</th>
                      <td><?php echo $apellidos; ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Nombre de usuario</th>
                      <td><?php echo $nombreusuario; ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Cedula</th>
                      <td><?php echo $cedula; ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Cargo</th>
                      <td><?php echo $cargo; ?></td>
                    </tr>
                    <tr>
                      <th scope="row">Fecha de nacimiento</th>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-md-4"></div>
          </div>
        <!--end::App Content-->
      </main>

<?php include("../layout/admin/parte2.php"); ?>
