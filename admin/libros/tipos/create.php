<?php
include ("../../../app/config/config.php");
include ("../../../app/config/conexion.php");
include ("../../../layout/admin/login.php");
include ("../../../layout/admin/datos_usuario.php");
include ("../../../layout/admin/comprueba_admin.php");
include("../../../layout/admin/parte1.php");

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
                            <h3 class="mb-0">Nuevo Tipo</h3>
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
<script>
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
    confirmButtonText: 'Sí, crear',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#0d6efd',
    cancelButtonColor: '#6c757d',
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}

// ===== Manejo de temas (tarjetas azules, sin toggle) =====
let temasAgregados = [];

function agregarTema() {
    const input = document.getElementById('temaInput');
    const nombre = input.value.trim();

    if (!nombre) return;

    // Evita duplicados
    if (temasAgregados.includes(nombre)) {
        input.value = '';
        return;
    }

    temasAgregados.push(nombre);
    renderTarjetaTema(nombre);
    actualizarHiddenTemas();

    input.value = '';
    input.focus();
}

function renderTarjetaTema(nombre) {
    const box = document.getElementById('temasBox');

    // Oculta el placeholder si es la primera tarjeta
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

function eliminarTema(nombre, card) {
    temasAgregados = temasAgregados.filter(t => t !== nombre);
    card.remove();
    actualizarHiddenTemas();

    // Si ya no quedan tarjetas, vuelve a mostrar el placeholder
    const box = document.getElementById('temasBox');
    if (temasAgregados.length === 0) {
        box.innerHTML = '<span class="text-muted small" id="temaPlaceholder">Aún no has agregado temas</span>';
    }
}

function actualizarHiddenTemas() {
    document.getElementById('temasHidden').value = temasAgregados.join(',');
}

// Enter en el input también agrega el tema
document.addEventListener('DOMContentLoaded', function() {
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