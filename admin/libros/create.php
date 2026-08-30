<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");
include ("../../layout/admin/comprueba_admin.php");
include("../../layout/admin/parte1.php");

$categorias= $pdo->prepare("SELECT id, nombre, foto FROM categorias");
$categorias->execute();
$id_categoria=[];
$categoria_nombre=[];
$foto=[];
$i=0;
foreach($categorias as $categoria){
    $id_categoria[$i]=$categoria['id'];
    $categoria_nombre[$i]=$categoria['nombre'];
    $foto[$i]=$categoria['foto'];
    $i++;
}
$tipos = $pdo->prepare("SELECT id, nombre FROM tipos");
$tipos->execute();
$id_tipo = [];
$nombre_tipo = [];
$i = 0;
foreach ($tipos as $tipo) {
    $id_tipo[$i] = $tipo['id'];
    $nombre_tipo[$i] = $tipo['nombre'];
    $i++;
}
?>
<style>
    .tema-card {
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-radius: 20px;
    cursor: pointer;
    user-select: none;
    font-size: 0.9rem;
    transition: all 0.15s ease;
    background-color: #f8f9fa;
}
.tema-card:hover {
    background-color: #e9ecef;
}
.tema-card.seleccionado {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
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
                            <h3 class="mb-0">Nuevo Libro</h3>
                        </div>
                    </div>
                    <hr>
                    <!--end::Row-->
                    <div class="card">
                        <h5 class="card-header">Llene la información con mucho cuidado</h5>
                        
                        <div class="card-body">
                            <form action="controller_create.php" method="post" enctype="multipart/form-data">    
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="titulo" class="form-label">Título</label>
                                        <input type="text" name="titulo" id="titulo" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="autor" class="form-label">Autor</label>
                                        <input type="text" name="autor" id="autor" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="editorial" class="form-label">Editorial</label>
                                        <input type="text" name="editorial" id="editorial" class="form-control" required>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <label for="edicion" class="form-label">Edición</label>
                                        <input type="text" name="edicion" id="edicion" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="ano" class="form-label">Año</label>
                                        <input type="number" name="ano" id="ano" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="idioma" class="form-label">Idioma</label>
                                        <select name="idioma" class="form-select" id="idioma" required>
                                            <option value="">-- Idioma --</option>
                                            <option value="Español">Español</option>
                                            <option value="Inglés">Inglés</option>
                                            <option value="Francés">Francés</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="disponibilidad" class="form-label">Disponibilidad</label>
                                        <select name="disponibilidad" class="form-select" id="disponibilidad" required>
                                            <option value="">-- Selecciona --</option>
                                            <option value="1">Disponible</option>
                                            <option value="0">No disponible</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tipo" class="form-label">Tipo</label>
                                        <select name="tipo" class="form-select" id="tipo" onchange="mostrarTemas(this)" required>
                                            <option value="">-- Selecciona --</option>
                                            <?php for ($i = 0; $i < count($id_tipo); $i++): ?>
                                                <option value="<?php echo htmlspecialchars($nombre_tipo[$i]); ?>"><?php echo htmlspecialchars($nombre_tipo[$i]); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Temas</label>
                                        
                                        <!-- Tarjetas de temas existentes (click para seleccionar) -->
                                        <div id="temasDisponiblesBox" class="d-flex flex-wrap gap-2 mb-2 p-2 border rounded" style="min-height: 50px;">
                                            <span class="text-muted small">Selecciona un tipo primero</span>
                                        </div>

                                        <!-- Input para crear un tema nuevo -->
                                        <div class="input-group">
                                            <input type="text" id="temaInput" class="form-control" placeholder="Nuevo tema..." disabled>
                                            <button type="button" class="btn btn-outline-secondary" id="btnAgregarTema" onclick="crearTema()" disabled>+ Agregar</button>
                                        </div>

                                        <!-- IDs/nombres de los temas seleccionados, para el submit -->
                                        <input type="hidden" name="temas" id="temasHidden">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cdd" class="form-label">CDD (Clasificación Decimal Dewey)</label>
                                        <input type="text" name="cdd" id="cdd" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="bloque" class="form-label">Bloque</label>
                                        <input type="text" name="bloque" id="bloque" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="categoria" class="form-label">Categoría</label>
                                        <select name="categoria" class="form-select" id="categoria" onchange="mostrarSubcategoria(this)" required>
                                            <option value="">-- Categoría --</option>
                                            <?php for ($i = 0; $i < count($id_categoria); $i++): ?>
                                                <option value="<?php echo htmlspecialchars($categoria_nombre[$i]); ?>"><?php echo htmlspecialchars($categoria_nombre[$i]); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="subcategoria" class="form-label">Subcategoría</label>
                                        <select name="subcategoria" id="subcategoria" class="form-select" disabled required>
                                            <option value="">-- Selecciona --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="seccion" class="form-label">Sección</label>
                                        <input type="text" name="seccion" id="seccion" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ejemplares" class="form-label">Ejemplares</label>
                                        <input type="number" name="ejemplares" id="ejemplares" class="form-control" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="prestados" class="form-label">Prestados</label>
                                        <input type="number" name="prestados" id="prestados" class="form-control" min="0" value="0" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Portada del libro (OPCIONAL)</label>
                                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                        <img id="preview" src="<?php echo $URL;?>/public/uploads/img/libros/default.jpg" 
                                            class="mt-2 rounded" width="80" height="110" style="object-fit:cover;">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Archivo PDF del libro (OPCIONAL)</label>
                                        <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf">
                                        <div id="pdfInfo" class="form-text"></div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin/libros" class="btn btn-default">Cancelar</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="button" onclick="confirmarRegistro(this)">Registrar Libro</button>
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

// Validación del PDF
document.getElementById('pdf').addEventListener('change', function() {
    const file = this.files[0];
    const info = document.getElementById('pdfInfo');

    if (!file) {
        info.textContent = '';
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();
    if (extension !== 'pdf') {
        alert('Solo se permiten archivos PDF');
        this.value = '';
        info.textContent = '';
        return;
    }

    const maxMB = 1024;
    if (file.size > maxMB * 1024 * 1024) {
        alert(`El archivo supera el tamaño máximo permitido (${maxMB} MB)`);
        this.value = '';
        info.textContent = '';
        return;
    }

    const tamañoMB = (file.size / (1024 * 1024)).toFixed(2);
    info.textContent = `Archivo seleccionado: ${file.name} (${tamañoMB} MB)`;
});
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
function mostrarSubcategoria(select) {
    const categoria = select.value;
    const subcategoriaSelect = document.getElementById('subcategoria');
    const btnNueva = document.getElementById('btnNuevaSub');

    // Limpiamos el select de subcategoría
    subcategoriaSelect.innerHTML = '<option value="">-- Selecciona --</option>';

    if (!categoria) {
        subcategoriaSelect.disabled = true;
        if (btnNueva) btnNueva.disabled = true;
        return;
    }

    fetch(`get_subcategorias.php?categoria=${encodeURIComponent(categoria)}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub.nombre;
                option.textContent = sub.nombre;
                subcategoriaSelect.appendChild(option);
            });
            subcategoriaSelect.disabled = false;
            if (btnNueva) btnNueva.disabled = false;
        })
        .catch(err => console.error('Error cargando subcategorías:', err));
}
let temasElegidos = []; // nombres de temas ya elegidos
let temasDisponibles = []; // temas existentes del tipo actual
let temasSeleccionados = []; // nombres de temas elegidos

function mostrarTemas(select) {
    const tipo = select.value;
    const input = document.getElementById('temaInput');
    const btnAgregar = document.getElementById('btnAgregarTema');
    const box = document.getElementById('temasDisponiblesBox');

    temasSeleccionados = [];
    actualizarHidden();
    box.innerHTML = '';
    input.value = '';

    if (!tipo) {
        input.disabled = true;
        btnAgregar.disabled = true;
        box.innerHTML = '<span class="text-muted small">Selecciona un tipo primero</span>';
        return;
    }

    box.innerHTML = '<span class="text-muted small">Cargando...</span>';

    fetch(`get_temas.php?tipo=${encodeURIComponent(tipo)}`)
        .then(res => res.json())
        .then(data => {
            box.innerHTML = '';

            if (data.length === 0) {
                box.innerHTML = '<span class="text-muted small">No hay temas aún, crea uno abajo</span>';
            } else {
                data.forEach(function(tema) {
                    renderTarjetaTema(tema.nombre);
                });
            }

            input.disabled = false;
            btnAgregar.disabled = false;
        })
        .catch(err => {
            console.error('Error cargando temas:', err);
            box.innerHTML = '<span class="text-danger small">Error al cargar temas</span>';
        });
}

function renderTarjetaTema(nombre) {
    const box = document.getElementById('temasDisponiblesBox');

    // Evitar duplicar la tarjeta si ya existe (ej. al crear una que acabamos de agregar)
    if (document.querySelector(`.tema-card[data-nombre="${CSS.escape(nombre)}"]`)) return;

    const card = document.createElement('span');
    card.className = 'tema-card';
    card.textContent = nombre;
    card.dataset.nombre = nombre;
    card.onclick = () => toggleTema(nombre, card);
    box.appendChild(card);
}

function toggleTema(nombre, card) {
    const index = temasSeleccionados.indexOf(nombre);

    if (index === -1) {
        temasSeleccionados.push(nombre);
        card.classList.add('seleccionado');
    } else {
        temasSeleccionados.splice(index, 1);
        card.classList.remove('seleccionado');
    }

    actualizarHidden();
}

function crearTema() {
    const tipo = document.getElementById('tipo').value;
    const input = document.getElementById('temaInput');
    const nombre = input.value.trim();

    if (!tipo) {
        alert('Primero selecciona un tipo');
        return;
    }
    if (!nombre) return;

    // Si ya existe como tarjeta, solo la seleccionamos (no la duplicamos)
    const existente = document.querySelector(`.tema-card[data-nombre="${CSS.escape(nombre)}"]`);
    if (existente) {
        if (!existente.classList.contains('seleccionado')) {
            toggleTema(nombre, existente);
        }
        input.value = '';
        return;
    }

    fetch('create_tema.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ tipo: tipo, nombre: nombre })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        // Quitamos el mensaje de "No hay temas aún" si estaba
        const box = document.getElementById('temasDisponiblesBox');
        const msgVacio = box.querySelector('.text-muted');
        if (msgVacio) msgVacio.remove();

        renderTarjetaTema(data.nombre);

        // La seleccionamos automáticamente, ya que el admin la acaba de crear
        const card = document.querySelector(`.tema-card[data-nombre="${CSS.escape(data.nombre)}"]`);
        toggleTema(data.nombre, card);

        input.value = '';
        input.focus(); // para seguir escribiendo temas rápido, uno tras otro
    })
    .catch(err => console.error('Error creando tema:', err));
}

function actualizarHidden() {
    document.getElementById('temasHidden').value = temasSeleccionados.join(',');
}

// Enter en el input también crea el tema (para agilidad, como pediste)
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('temaInput');
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                crearTema();
            }
        });
    }
});
</script>

<?php include("../../layout/admin/parte2.php");?>