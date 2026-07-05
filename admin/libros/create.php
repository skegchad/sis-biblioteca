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
                                    <div class="col-md-4">
                                        <label for="titulo" class="form-label">Título</label>
                                        <input type="text" name="titulo" id="titulo" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="editorial" class="form-label">Editorial</label>
                                        <input type="text" name="editorial" id="editorial" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edicion" class="form-label">Edición</label>
                                        <input type="text" name="edicion" id="edicion" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="ano" class="form-label">Año</label>
                                        <input type="number" name="ano" id="ano" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="idioma" class="form-label">Idioma</label>
                                        <select name="idioma" class="form-select" id="idioma" required>
                                            <option value="">-- Idioma --</option>
                                            <option value="Español">Español</option>
                                            <option value="Inglés">Inglés</option>
                                            <option value="Francés">Francés</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="disponibilidad" class="form-label">Disponibilidad</label>
                                        <select name="disponibilidad" class="form-select" id="disponibilidad" required>
                                            <option value="">-- Selecciona --</option>
                                            <option value="Disponible">Disponible</option>
                                            <option value="No disponible">No disponible</option>
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
                                        <label for="temas" class="form-label">Temas</label>
                                        <input type="text" name="temas" id="temas" class="form-control" required>
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
                                            <option value="Ciencias Sociales">Ciencias Sociales</option>
                                            <option value="Literatura Retorica">Literatura RETÓRICA</option>
                                            <option value="Tecnologia">Tecnología</option>
                                            <option value="Religion">Religión</option>
                                            <option value="Filosofia y Psicologia">Filosofia y Psicologia</option>
                                            <option value="Filosofia y Psicologia">Filosofia y Psicologia</option>
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
                                    <div class="mb-3">
                                        <label class="form-label">Portada del libro (OPCIONAL)</label>
                                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                        
                                        <!-- Preview opcional -->
                                        <img id="preview" src="<?php echo $URL;?>/public/uploads/img/libros/default.jpg" 
                                            class="mt-2 rounded" width="80" height="110" style="object-fit:cover;">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 d-grid gap-2">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo $URL; ?>/admin" class="btn btn-default">Cancelar</a>
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

    // Definimos las subcategorías posibles para cada categoría
    const subcategorias = {
        "Ficción": ["Novela", "Cuento", "Poesía", "Fantasía", "Ciencia Ficción"],
        "No ficción": ["Biografía", "Historia", "Ensayo", "Autoayuda"],
        "Técnico": ["Informática", "Matemáticas", "Ingeniería", "Ciencias"],
        "Referencia": ["Diccionario", "Enciclopedia", "Manual"]
    };

    // Limpiamos el select de subcategoría
    subcategoriaSelect.innerHTML = '<option value="">-- Selecciona --</option>';

    if (categoria && subcategorias[categoria]) {
        subcategorias[categoria].forEach(function(sub) {
            const option = document.createElement('option');
            option.value = sub;
            option.textContent = sub;
            subcategoriaSelect.appendChild(option);
        });
        subcategoriaSelect.disabled = false;
    } else {
        subcategoriaSelect.disabled = true;
    }
}
</script>

<?php include("../../layout/admin/parte2.php");?>