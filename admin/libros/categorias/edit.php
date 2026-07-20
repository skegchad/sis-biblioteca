<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/datos_usuario.php");
include ("../../../layout/admin/comprueba_admin.php");
include("../../../layout/admin/parte1.php");

$id_get = $_GET['id'] ?? null;

if (!$id_get) {
    header("Location: " . $URL . "/admin/categorias?error=id_invalido");
    exit;
}

// ===== Traer la categoría =====
$query = $pdo->prepare("SELECT * FROM categorias WHERE id = :id");
$query->execute([':id' => $id_get]);
$categoria = $query->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header("Location: " . $URL . "/admin/categorias?error=no_encontrada");
    exit;
}

$nombre = trim($categoria['nombre']);
$foto   = trim($categoria['foto']);

// ===== Traer las subcategorías de esta categoría =====
$querySub = $pdo->prepare("SELECT id, nombre FROM subcategorias WHERE categoria_id = :id");
$querySub->execute([':id' => $id_get]);
$subcategorias = $querySub->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .subcategoria-card {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #0d6efd;
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
    }
    .subcategoria-card .btn-eliminar-tag {
        cursor: pointer;
        font-weight: bold;
        line-height: 1;
    }
    .subcategoria-card .btn-eliminar-tag:hover {
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
                            <h3 class="mb-0">Editar Categoria</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header">Llene la información con mucho cuidado</h5>
                        
                        <div class="card-body">
                            <form action="controller_edit.php?id=<?php echo $id_get; ?>" method="post" enctype="multipart/form-data">    
                                <input type="hidden" name="id" value="<?php echo $id_get; ?>">

                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="Nombre" class="form-label">Nombre</label>
                                        <input type="text" value="<?php echo htmlspecialchars($nombre); ?>" name="Nombre" id="Nombre" class="form-control" required>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Subcategorías</label>

                                        <!-- Tarjetas de subcategorías agregadas -->
                                        <div id="subcategoriasBox" class="d-flex flex-wrap gap-2 mb-2 p-2 border rounded" style="min-height: 50px;">
                                            <?php if (empty($subcategorias)): ?>
                                                <span class="text-muted small" id="subcatPlaceholder">Aún no has agregado subcategorías</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Input para agregar una subcategoría -->
                                        <div class="input-group">
                                            <input type="text" id="subcategoriaInput" class="form-control" placeholder="Nueva subcategoría...">
                                            <button type="button" class="btn btn-outline-secondary" id="btnAgregarSubcategoria" onclick="agregarSubcategoria()">+ Agregar</button>
                                        </div>

                                        <!-- Nombres de las subcategorías, para el submit -->
                                        <input type="hidden" name="subcategorias" id="subcategoriasHidden">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Foto de la categoria (OPCIONAL)</label>
                                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                        <img id="preview" src="<?php echo $URL;?>/<?php echo $foto; ?>" 
                                            class="mt-2 rounded" width="110" height="110" style="object-fit:cover;">
                                        <small class="text-muted d-block mt-1">Deja este campo vacío si no quieres cambiar la foto actual.</small>
                                    </div>
                                    
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin/libros/categorias" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" type="button" onclick="confirmarRegistro(this)">Actualizar Categoría</button>
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
// Preview de la foto
document.getElementById('foto').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
    }
});

function confirmarRegistro(btn) {
  const form = btn.closest('form');

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Esta información es correcta?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, actualizar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#00ff22',
    cancelButtonColor: '#6c757d',
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}

// ===== Manejo de subcategorías (tarjetas azules, sin toggle) =====
// Precargamos las subcategorías que ya tiene la categoría
let subcategoriasAgregadas = <?php echo json_encode(array_column($subcategorias, 'nombre')); ?>;

function renderTarjetasIniciales() {
    subcategoriasAgregadas.forEach(function(nombre) {
        renderTarjetaSubcategoria(nombre);
    });
    actualizarHiddenSubcategorias();
}

function agregarSubcategoria() {
    const input = document.getElementById('subcategoriaInput');
    const nombre = input.value.trim();

    if (!nombre) return;

    // Evita duplicados
    if (subcategoriasAgregadas.includes(nombre)) {
        input.value = '';
        return;
    }

    subcategoriasAgregadas.push(nombre);
    renderTarjetaSubcategoria(nombre);
    actualizarHiddenSubcategorias();

    input.value = '';
    input.focus();
}

function renderTarjetaSubcategoria(nombre) {
    const box = document.getElementById('subcategoriasBox');

    // Oculta el placeholder si está presente
    const placeholder = document.getElementById('subcatPlaceholder');
    if (placeholder) placeholder.remove();

    const card = document.createElement('span');
    card.className = 'subcategoria-card';
    card.dataset.nombre = nombre;

    const texto = document.createElement('span');
    texto.textContent = nombre;

    const btnEliminar = document.createElement('span');
    btnEliminar.className = 'btn-eliminar-tag';
    btnEliminar.textContent = '×';
    btnEliminar.onclick = () => eliminarSubcategoria(nombre, card);

    card.appendChild(texto);
    card.appendChild(btnEliminar);
    box.appendChild(card);
}

function eliminarSubcategoria(nombre, card) {
    subcategoriasAgregadas = subcategoriasAgregadas.filter(s => s !== nombre);
    card.remove();
    actualizarHiddenSubcategorias();

    // Si ya no quedan tarjetas, vuelve a mostrar el placeholder
    const box = document.getElementById('subcategoriasBox');
    if (subcategoriasAgregadas.length === 0) {
        box.innerHTML = '<span class="text-muted small" id="subcatPlaceholder">Aún no has agregado subcategorías</span>';
    }
}

function actualizarHiddenSubcategorias() {
    document.getElementById('subcategoriasHidden').value = subcategoriasAgregadas.join(',');
}

// Enter en el input también agrega la subcategoría
document.addEventListener('DOMContentLoaded', function() {
    renderTarjetasIniciales();

    const input = document.getElementById('subcategoriaInput');
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                agregarSubcategoria();
            }
        });
    }
});
</script>

<?php include("../../../layout/admin/parte2.php");?>