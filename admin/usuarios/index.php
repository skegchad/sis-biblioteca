<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include("../../layout/admin/parte1.php");
$contador = 0;?>

<style>
#toast-success {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #198754;
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: entrar 0.4s ease, salir 0.5s ease 3s forwards;
}
#toast-success-eliminado {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #ff0000;
    color: white;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: entrar 0.4s ease, salir 0.5s ease 3s forwards;
}

@keyframes entrar {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

@keyframes salir {
    from { transform: translateX(0);    opacity: 1; }
    to   { transform: translateX(120%); opacity: 0; }
}
/* Contenedor de paginación */
.dataTables_paginate {
  margin-top: 10px;
}

/* Todos los botones */
.dataTables_paginate .paginate_button {
  display: inline-block;
  padding: 6px 12px;
  margin: 0 2px;
  border-radius: 6px;
  border: 1px solid #3b3b3b;
  cursor: pointer;
  background: #3b3b3b;
  color: #ffffff !important;
  transition: all 0.2s;
}

/* Hover */
.dataTables_paginate .paginate_button:hover {
  background: #0d6efd;
  color: #ffffff !important;
  border-color: #0d6efd;
}

/* Página activa */
.dataTables_paginate .paginate_button.current {
  background: #0d6efd;
  color: #ffffff !important;
  border-color: #0d6efd;
  font-weight: bold;
}

/* Botones deshabilitados */
.dataTables_paginate .paginate_button.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.dataTables_wrapper {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

/* "Mostrar X registros" → izquierda arriba */
.dataTables_length {
  order: 1;
  flex: 1;
}

/* Buscador → derecha arriba */
.dataTables_filter {
  order: 2;
  text-align: right;
}

/* Info ("Mostrando 1 a 10...") → abajo izquierda */
.dataTables_info {
  order: 3;
  flex: 1;
}

/* Paginación → abajo derecha */
.dataTables_paginate {
  order: 4;
}

/* La tabla ocupa todo el ancho */
.dataTables_scroll,
table.dataTable {
  order: 5;
  width: 100% !important;
  flex-basis: 100%;
}
</style>

<?php if(isset($_GET['success']) && $_GET['success'] === 'registrado'): ?>
<div id="toast-success">
    <i class="ti ti-circle-check"></i> ¡Usuario registrado!
</div>
<?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] === 'actualizado'): ?>
<div id="toast-success">
    <i class="ti ti-circle-check"></i> ¡Usuario actualizado!
</div>
<?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] === 'eliminado'): ?>
<div id="toast-success-eliminado">
    <i class="bi bi-trash"></i> ¡Usuario eliminado!
</div>
<?php endif; ?>

    <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Listado de usuarios</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <script>
                        $(document).ready(function() {
                            $('#tablaUsuarios').DataTable({
                                pageLength: 5,        // filas por página
                                ordering: true,        // ordenar columnas al hacer clic
                                searching: true,       // buscador (true por defecto)
                                language: {
                                    search: "Buscar:",
                                    lengthMenu: "Mostrar _MENU_ registros",
                                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                    paginate: {
                                    previous: "Anterior",
                                    next: "Siguiente"
                                    }
                                }
                                });
                        });
                    </script>
                    <table id="tablaUsuarios" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Num</th>
                                <th scope="col">Nombres</th>
                                <th scope="col">Apellidos</th>
                                <th scope="col">Cedula</th>
                                <th scope="col">Nombre de Usuario</th>
                                <th scope="col">Cargo</th>
                                <th scope="col"><center>Acciones</center></th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php
                                $query = $pdo->prepare('SELECT * FROM tb_usuarios WHERE estado="1" ');
                                $query->execute();
                                $usuarios=$query->fetchAll(PDO::FETCH_ASSOC);
                                foreach($usuarios as $usuario){

                                    $id = $usuario['id_usuario'];
                                    $nombre = $usuario['nombre_completo'];
                                    $apellidos = $usuario['apellidos'];
                                    $nombreusuario = $usuario['nombre_usuario'];
                                    $cedula = $usuario['cedula'];
                                    $cargo = $usuario['cargo'];
                                    $contador = $contador +1;
                                ?>
                                    <tr>
                                        <td><?php echo $contador;?></td>
                                        <td><?php echo $nombre;?></td>
                                        <td><?php echo $apellidos;?></td>
                                        <td><?php echo $cedula;?></td>
                                        <td><?php echo $nombreusuario;?></td>
                                        <td><?php echo $cargo;?></td>
                                        <td>
                                            <center>
                                                <a href="edit.php?id=<?php echo $id;?>" class="btn btn-success btn-sm">
                                                    <i class="ti ti-edit"></i> Editar
                                                </a>
                                                <a href="erase.php?id=<?php echo $id;?>" class="btn btn-danger btn-sm">
                                                    <i class="ti ti-trash"></i> Borrar
                                                </a>
                                            </center>
                                        </td>
                                    </tr>
                                <?php
                                }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            <!--end::Container-->
            </div>
    </main>
<?php include("../../layout/admin/parte2.php");?>

<script>
    // Lo elimina del DOM después de que termina la animación
    setTimeout(() => {
        const toast = document.getElementById('toast-success');
        if (toast) toast.remove();
    }, 3600);
</script>