<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/datos_usuario.php");
include ("../../../layout/admin/comprueba_admin.php");
include("../../../layout/admin/parte1.php");

$id_get = $_GET['id'] ?? null;

if (!$id_get) {
    header("Location: " . $URL . "/admin/libros/tipos?error=id_invalido");
    exit;
}

// ===== Traer el tipo =====
$query = $pdo->prepare("SELECT * FROM tipos WHERE id = :id");
$query->execute([':id' => $id_get]);
$tipo = $query->fetch(PDO::FETCH_ASSOC);

if (!$tipo) {
    header("Location: " . $URL . "/admin/libros/tipos?error=no_encontrado");
    exit;
}

$nombre = trim($tipo['nombre']);

// ===== Traer los temas de este tipo =====
$queryTemas = $pdo->prepare("SELECT id, nombre FROM temas WHERE tipo_id = :id");
$queryTemas->execute([':id' => $id_get]);
$temas = $queryTemas->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .tema-card {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #0d6efd;
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
    }
    .tema-card .btn-eliminar-tag {
        cursor: pointer;
        font-weight: bold;
        line-height: 1;
    }
    .tema-card .btn-eliminar-tag:hover {
        opacity: 0.7;
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
                            <h3 class="mb-0">Borrar Tipo</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header">Llene la información con mucho cuidado</h5>
                        
                        <div class="card-body">
                            <form action="controller_erase.php?id=<?php echo $id_get; ?>" method="post"> 
                                <fieldset disabled>
                                <input type="hidden" name="id" value="<?php echo $id_get; ?>">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" value="<?php echo htmlspecialchars($nombre); ?>" name="Nombre" id="Nombre" class="form-control" required>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Temas</label>

                                        <!-- Tarjetas de temas agregados -->
                                        <div id="temasBox" class="d-flex flex-wrap gap-2 mb-2 p-2 border rounded" style="min-height: 50px;">
                                            <?php if (empty($temas)): ?>
                                                <span class="text-muted small" id="temaPlaceholder">Aún no has agregado temas</span>
                                            <?php endif; ?>
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
                                </fieldset>   
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin/libros/tipos" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-danger" type="button" onclick="confirmarRegistro(this)">Borrar Tipo</button>
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

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Seguro que quieres eliminar este tipo? \n Se eliminaran todos los temas y tendras que actualizar manualmente los libros que los tenian',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, borrar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ff0000',
    cancelButtonColor: '#6c757d',
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}

// ===== Manejo de temas (tarjetas azules, sin toggle) =====
// Precargamos los temas que ya tiene el tipo
let temasAgregados = <?php echo json_encode(array_column($temas, 'nombre')); ?>;

function renderTarjetasIniciales() {
    temasAgregados.forEach(function(nombre) {
        renderTarjetaTema(nombre);
    });
    actualizarHiddenTemas();
}

function renderTarjetaTema(nombre) {
    const box = document.getElementById('temasBox');

    // Oculta el placeholder si está presente
    const placeholder = document.getElementById('temaPlaceholder');
    if (placeholder) placeholder.remove();

    const card = document.createElement('span');
    card.className = 'tema-card';
    card.dataset.nombre = nombre;

    const texto = document.createElement('span');
    texto.textContent = nombre;

    const btnEliminar = document.createElement('span');
    btnEliminar.className = 'btn-eliminar-tag';
    btnEliminar.textContent = '×';
    btnEliminar.onclick = () => eliminarTema(nombre, card);

    card.appendChild(texto);
    card.appendChild(btnEliminar);
    box.appendChild(card);
}
function actualizarHiddenTemas() {
    document.getElementById('temasHidden').value = temasAgregados.join(',');
}

// Enter en el input también agrega el tema
document.addEventListener('DOMContentLoaded', function() {
    renderTarjetasIniciales();

    const input = document.getElementById('temaInput');
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                agregarTema();
            }
        });
    }
});
</script>

<?php include("../../../layout/admin/parte2.php");?>