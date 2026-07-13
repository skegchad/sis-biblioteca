<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include ("../../layout/admin/comprueba_admin.php");
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

/* Limita el tamaño de la celda de descripción */
.celda-descripcion {
    max-width: 220px;
    max-height: 80px;
    overflow-y: auto;
    display: block;
    white-space: normal;
}

.celda-ruta {
    max-width: 120px;
    max-height: 100x;
    overflow-y: auto;
    display: block;
    white-space: normal;
}
</style>

<?php if(isset($_GET['success']) && $_GET['success'] === 'registrado'): ?>
<div id="toast-success">
    <i class="ti ti-circle-check"></i> ¡Libro registrado!
</div>
<?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] === 'actualizado'): ?>
<div id="toast-success">
    <i class="ti ti-circle-check"></i> ¡Libro actualizado!
</div>
<?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success'] === 'eliminado'): ?>
<div id="toast-success-eliminado">
    <i class="bi bi-trash"></i> ¡Libro eliminado!
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
                            <h3 class="mb-0">Listado de libros</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <script>
                        $(document).ready(function() {
                        $('#tablaLibros').DataTable({
                            pageLength: 5,
                            ordering: true,
                            searching: true,
                            scrollX: true,        // <-- activa el scroll horizontal SOLO de la tabla
                            scrollCollapse: true,
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
                    <table id="tablaLibros" class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Num</th>
                                <th scope="col">Título</th>
                                <th scope="col">Autor</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Idioma</th>
                                <th scope="col">Disponibilidad</th>
                                <th scope="col">Temas</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Edición</th>
                                <th scope="col">Año</th>
                                <th scope="col">CDD</th>
                                <th scope="col">Bloque</th>
                                <th scope="col">Categoría</th>
                                <th scope="col">Subcategoría</th>
                                <th scope="col">Sección</th>
                                <th scope="col">Editorial</th>
                                <th scope="col">Ejemplares</th>
                                <th scope="col">Prestados</th>
                                <th scope="col">Foto</th>
                                <th scope="col">Pdf</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php
                                $query = $pdo->prepare('
                                    SELECT 
                                        l.*,
                                        GROUP_CONCAT(t.nombre SEPARATOR ", ") AS temas
                                    FROM tb_libros l
                                    LEFT JOIN libro_tema lt ON l.id_libro = lt.id_libro
                                    LEFT JOIN temas t ON lt.tema_id = t.id
                                    WHERE l.estado = "1"
                                    GROUP BY l.id_libro
                                ');
                                $query->execute();
                                $libros = $query->fetchAll(PDO::FETCH_ASSOC);
                                foreach($libros as $libro){

                                    $id = $libro['id_libro'];
                                    $titulo = $libro['titulo'];
                                    $autor = $libro['autor'];
                                    $descripcion = $libro['descripcion'];
                                    $idioma = $libro['idioma'];
                                    $disponibilidad = $libro['disponibilidad'];
                                    $temas = $libro['temas'];
                                    $tipo = $libro['tipo'];
                                    $edicion = $libro['edicion'];
                                    $ano = $libro['ano'];
                                    $cdd = $libro['cdd'];
                                    $bloque = $libro['bloque'];
                                    $categoria = $libro['categoria'];
                                    $subcategoria = $libro['subcategoria'];
                                    $seccion = $libro['seccion'];
                                    $editorial = $libro['editorial'];
                                    $ejemplares = $libro['ejemplares'];
                                    $prestados = $libro['prestados'];
                                    $ruta_pdf = $libro['ruta_pdf'];
                                    $ruta_foto = $libro['ruta_foto'];

                                    $contador = $contador +1;
                                ?>
                                    <tr>
                                        <td><?php echo $contador;?></td>
                                        <td><?php echo $titulo;?></td>
                                        <td><?php echo $autor;?></td>
                                        <td>
                                            <span class="celda-descripcion"><?php echo $descripcion;?></span>
                                        </td>
                                        <td><?php echo $idioma;?></td>
                                        <td><?php echo $disponibilidad;?></td>
                                        <td><?php echo $temas;?></td>
                                        <td><?php echo $tipo;?></td>
                                        <td><?php echo $edicion;?></td>
                                        <td><?php echo $ano;?></td>
                                        <td><?php echo $cdd;?></td>
                                        <td><?php echo $bloque;?></td>
                                        <td><?php echo $categoria;?></td>
                                        <td><?php echo $subcategoria;?></td>
                                        <td><?php echo $seccion;?></td>
                                        <td><?php echo $editorial;?></td>
                                        <td><?php echo $ejemplares;?></td>
                                        <td><?php echo $prestados;?></td>
                                        <td>
                                            <span class="celda-ruta"><?php echo $ruta_foto;?></span>
                                            <div class="mt-2">
                                                <a href="<?php echo $URL;?>/<?php echo $ruta_foto ?>">
                                                    <img id="preview" src="<?php echo $URL;?>/<?php echo $ruta_foto ?>"
                                                        class="mt-2 rounded" width="80" height="110" style="object-fit:cover;">
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="celda-ruta"><?php echo $ruta_pdf;?></span>
                                            <div class="mt-2">
                                                <a href="<?php echo $URL . '/' . $ruta_pdf; ?>"
                                                target="_blank"
                                                class="btn btn-outline-danger btn-sm">
                                                    📄 Ver PDF actual
                                                </a>
                                            </div>
                                        </td>

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