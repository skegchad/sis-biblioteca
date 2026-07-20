<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include ("../../layout/admin/comprueba_admin.php");
include("../../layout/admin/parte1.php"); 
// --- Libros con al menos un ejemplar disponible, para el select ---
$stmtLibros = $pdo->prepare("
    SELECT id_libro, titulo, autor, ejemplares, prestados
    FROM tb_libros
    WHERE estado = '1'
      AND fyh_eliminacion IS NULL
      AND (ejemplares - prestados) > 0
    ORDER BY titulo ASC
");
$stmtLibros->execute();
$librosDisponibles = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Select2 (para el buscador de libros) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css">
<style>
/* Margen superior */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 10px;
}

/* Todos los botones */
.dataTables_wrapper .dataTables_paginate .paginate_button .page-link{
    background: #3b3b3b;
    color: #fff !important;
    border: 1px solid #3b3b3b;
    border-radius: 6px;
    margin: 0 2px;
    transition: .2s;
}

/* Hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:not(.disabled):hover .page-link{
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff !important;
}

/* Página activa */
.dataTables_wrapper .dataTables_paginate .paginate_button.active .page-link{
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff !important;
    font-weight: bold;
}

/* Deshabilitado */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled .page-link{
    opacity: .45;
    cursor: not-allowed;
}

/* Quitar el fondo azul de Bootstrap al enfocar */
.page-link:focus{
    box-shadow:none;
}

/* ===== Tema oscuro ===== */
[data-bs-theme="dark"] .select2-container--default .select2-selection--single {
    background-color: #212529;
    border: 1px solid #495057;
    color: #fff;
    height: 38px;
}

[data-bs-theme="dark"] .select2-container--default .select2-selection__rendered {
    color: #fff;
    line-height: 36px;
}

[data-bs-theme="dark"] .select2-container--default .select2-selection__arrow b {
    border-top-color: #fff;
}

[data-bs-theme="dark"] .select2-dropdown {
    background-color: #212529;
    border-color: #495057;
}

[data-bs-theme="dark"] .select2-search__field {
    background-color: #343a40;
    color: #fff;
    border: 1px solid #495057;
}

[data-bs-theme="dark"] .select2-results__option {
    color: #fff;
    background-color: #212529;
}

[data-bs-theme="dark"] .select2-results__option--highlighted {
    background-color: #0d6efd !important;
    color: #fff;
}

/* ===== Tema claro ===== */
[data-bs-theme="light"] .select2-container--default .select2-selection--single {
    background-color: #fff;
    color: #212529;
}

</style>

    <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Nuevo Préstamo</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header"></h5>

                        <div class="card-body">
                            <form action="controller_create.php" method="post" id="formPrestamo">
                                <div class="row">
                                    <!-- Libro -->
                                    <div class="col-md-6">
                                        <label for="id_libro" class="form-label">Libro</label>
                                        <select name="id_libro" id="id_libro" class="form-select" style="width:100%">
                                            <option value="">-- Selecciona un libro --</option>
                                            <?php foreach ($librosDisponibles as $libro): ?>
                                                <?php $disponibles = (int)$libro['ejemplares'] - (int)$libro['prestados']; ?>
                                                <option value="<?= (int)$libro['id_libro'] ?>">
                                                    <?= htmlspecialchars($libro['titulo']) ?> — <?= htmlspecialchars($libro['autor']) ?>
                                                    (<?= $disponibles ?> disponible<?= $disponibles === 1 ? '' : 's' ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($librosDisponibles)): ?>
                                            <div class="form-text text-danger">No hay libros con ejemplares disponibles en este momento.</div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Usuario -->
                                    <div class="col-md-6">
                                        <label class="form-label">Usuario</label>
                                        <div class="input-group">
                                            <input type="text" id="usuarioSeleccionadoTexto" class="form-control" placeholder="Ningún usuario seleccionado" readonly>
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUsuarios">
                                                Seleccionar
                                            </button>
                                        </div>
                                        <input type="hidden" name="id_usuario" id="id_usuario">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- Fecha de devolución -->
                                    <div class="col-md-4">
                                        <label for="fecha_devolucion" class="form-label">Fecha de devolución</label>
                                        <input
                                            type="date"
                                            name="fecha_devolucion"
                                            id="fecha_devolucion"
                                            class="form-control"
                                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                            required
                                        >
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin/prestamos" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="button" onclick="confirmarRegistro(this)">Registrar Préstamo</button>
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

    <!-- Modal: selección de usuario -->
    <div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filtroCargo" class="form-label">Filtrar por cargo</label>
                            <select id="filtroCargo" class="form-select">
                                <option value="">Todos</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Docente">Docente</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Público">Público</option>
                            </select>
                        </div>
                    </div>

                    <table id="tablaUsuarios" class="table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>Nombre completo</th>
                                <th>Cargo</th>
                                <th>Curso</th>
                                <th>Paralelo/Especialidad</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
let tablaUsuarios = null;

// La tabla se inicializa recién cuando se abre el modal (no antes), para
// que DataTables calcule bien los anchos de columna sobre un contenedor
// visible.
document.getElementById('modalUsuarios').addEventListener('shown.bs.modal', function () {
    if (tablaUsuarios === null) {
        tablaUsuarios = $('#tablaUsuarios').DataTable({
            ajax: {
                url: 'ajax_usuarios.php',
                dataSrc: ''
            },
            columns: [
                { data: 'nombre_completo' },
                { data: 'cargo' },
                {
                    data: null,
                    render: function (usuario) {
                        return (usuario.cargo === 'Estudiante' && usuario.curso)
                            ? usuario.curso
                            : 'Sin curso';
                    }
                },
                {
                    data: null,
                    render: function (usuario) {
                        return (usuario.cargo === 'Estudiante' && usuario.paralelo)
                            ? usuario.paralelo
                            : 'Sin paralelo';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (usuario) {
                        return '<button type="button" class="btn btn-sm btn-primary btn-elegir-usuario" '
                            + 'data-id="' + usuario.id_usuario + '" '
                            + 'data-nombre="' + $('<div>').text(usuario.nombre_completo).html() + '">'
                            + 'Seleccionar</button>';
                    }
                }
            ],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ usuarios',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
                infoEmpty: 'No hay usuarios para mostrar',
                infoFiltered: '(filtrado de _MAX_ usuarios en total)',
                zeroRecords: 'No se encontraron usuarios con ese filtro',
                paginate: { previous: 'Anterior', next: 'Siguiente' }
            }
        });
    } else {
        tablaUsuarios.ajax.reload();
    }
});

// Filtro por cargo: busca exactamente el valor de la columna "Cargo".
document.getElementById('filtroCargo').addEventListener('change', function () {
    const valor = this.value;
    tablaUsuarios.column(1).search(valor ? '^' + valor + '$' : '', true, false).draw();
});

// Click en "Seleccionar" dentro de una fila de la tabla.
$('#tablaUsuarios tbody').on('click', '.btn-elegir-usuario', function () {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre');

    document.getElementById('id_usuario').value = id;
    document.getElementById('usuarioSeleccionadoTexto').value = nombre;

    bootstrap.Modal.getInstance(document.getElementById('modalUsuarios')).hide();
});

// Select2 para el buscador de libros.
$('#id_libro').select2({
    placeholder: '-- Selecciona un libro --',
    width: '100%'
});

// Validación + confirmación antes de enviar el formulario.
function confirmarRegistro(boton) {
    const idLibro = document.getElementById('id_libro').value;
    const idUsuario = document.getElementById('id_usuario').value;
    const fechaDevolucion = document.getElementById('fecha_devolucion').value;

    if (!idLibro) {
        Swal.fire('Falta el libro', 'Selecciona un libro para el préstamo.', 'warning');
        return;
    }
    if (!idUsuario) {
        Swal.fire('Falta el usuario', 'Selecciona un usuario para el préstamo.', 'warning');
        return;
    }
    if (!fechaDevolucion) {
        Swal.fire('Falta la fecha', 'Selecciona una fecha de devolución.', 'warning');
        return;
    }

    Swal.fire({
        title: '¿Registrar préstamo?',
        text: 'Se registrará el préstamo con los datos seleccionados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then(function (resultado) {
        if (resultado.isConfirmed) {
            document.getElementById('formPrestamo').submit();
        }
    });
}
</script>

<?php include("../../layout/admin/parte2.php"); ?>