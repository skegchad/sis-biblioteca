
console.log("ESTANTERIA.JS CARGADO");
console.log("APP_URL:", window.APP_URL);
(function () {

  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;

  console.log("BASE:", BASE);
  console.log("API_BUSCAR:", API_BUSCAR);
  console.log("API_FILTROS:", API_FILTROS);

  const cont = document.getElementById('estante');
  const selCat = document.getElementById('f-categoria');
  const selTema = document.getElementById('f-tema');
  const inputBuscar = document.getElementById('f-buscar');

  /* Ancho del lomo según número de páginas (volumen del modelo 3D) */
  function anchoPorPaginas(paginas) {
    const MIN_PAG = 100, MAX_PAG = 800;
    const MIN_ANCHO = 20, MAX_ANCHO = 52;
    const p = Math.max(MIN_PAG, Math.min(MAX_PAG, paginas));
    const t = (p - MIN_PAG) / (MAX_PAG - MIN_PAG);
    return Math.round(MIN_ANCHO + t * (MAX_ANCHO - MIN_ANCHO));
  }

  /* Texto blanco o negro según contraste con el color dominante */
  function colorTexto(hex) {
    const c = hex.replace('#', '');
    const r = parseInt(c.substr(0, 2), 16), g = parseInt(c.substr(2, 2), 16), b = parseInt(c.substr(4, 2), 16);
    const luminancia = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminancia > 0.55 ? '#20201C' : '#F7F4EC';
  }

  function sombrear(hex, porcentaje) {
    const c = hex.replace('#', '');
    let r = parseInt(c.substr(0, 2), 16), g = parseInt(c.substr(2, 2), 16), b = parseInt(c.substr(4, 2), 16);
    const f = porcentaje / 100;
    r = Math.round(r + (f < 0 ? r : 255 - r) * f);
    g = Math.round(g + (f < 0 ? g : 255 - g) * f);
    b = Math.round(b + (f < 0 ? b : 255 - b) * f);
    return `rgb(${r},${g},${b})`;
  }

  function crearLibroDOM(libro) {
    const ancho = anchoPorPaginas(libro.paginas);
    const texto = colorTexto(libro.color);
    const rutaFoto = libro.ruta_foto ? `${BASE}/${libro.ruta_foto}`.replace(/([^:]\/)\/+/g, '$1') : '';

    const el = document.createElement('div');
    el.className = 'libro';
    el.style.width = ancho + 'px';
    el.dataset.id = libro.id;

    // La portada usa la foto real si existe; si no, un degradado con el color dominante
    const fondoPortada = rutaFoto
      ? `url('${rutaFoto}')`
      : `linear-gradient(160deg, ${libro.color} 0%, ${sombrear(libro.color, -18)} 100%)`;

    el.innerHTML = `
      <div class="libro-inner">
        <div class="cara lomo" style="background:${libro.color}; color:${texto};">
          <span class="titulo-lomo">${libro.titulo}</span>
        </div>
        <div class="cara portada" style="background-image:${fondoPortada};">
          <span class="paginas-tag">${libro.paginas} pág.</span>
          <div class="etiqueta">
            <b>${libro.titulo}</b>
            ${libro.autor}
          </div>
        </div>
      </div>
    `;

    el.addEventListener('click', () => {
      const yaAbierto = el.classList.contains('abierto');
      document.querySelectorAll('.libro.abierto').forEach(l => {
        l.classList.remove('abierto');
        l.querySelector('.libro-inner').style.width = '';
      });
      if (!yaAbierto) {
        el.classList.add('abierto');
        el.querySelector('.libro-inner').style.width = Math.max(ancho, 150) + 'px';
      }
    });

    return el;
  }

  function pintarEstante(libros) {
    cont.innerHTML = '';
    if (!libros.length) {
      cont.innerHTML = '<p class="sin-resultados">No se encontraron libros con esos filtros.</p>';
      return;
    }
    libros.forEach(l => cont.appendChild(crearLibroDOM(l)));
  }

  async function cargarFiltros() {
    try {
      const res = await fetch(API_FILTROS);
      const data = await res.json();
      selCat.innerHTML = '<option value="">Todas las categorías</option>' +
        data.categorias.map(c => `<option value="${c}">${c}</option>`).join('');
      selTema.innerHTML = '<option value="">Todos los temas</option>' +
        data.temas.map(t => `<option value="${t}">${t}</option>`).join('');
    } catch (err) {
      console.error('No se pudieron cargar los filtros', err);
    }
  }

  async function buscarLibros() {
    const params = new URLSearchParams({
      categoria: selCat.value || '',
      tema: selTema.value || '',
      buscar: inputBuscar.value.trim() || '',
    });
    try {
      cont.innerHTML = '<p class="sin-resultados">Cargando…</p>';
      const res = await fetch(`${API_BUSCAR}?${params.toString()}`);
      const libros = await res.json();
      pintarEstante(libros);
    } catch (err) {
      console.error('Error buscando libros', err);
      cont.innerHTML = '<p class="sin-resultados">Ocurrió un error cargando los libros.</p>';
    }
  }

  let temporizador;
  function buscarConDebounce() {
    clearTimeout(temporizador);
    temporizador = setTimeout(buscarLibros, 300);
  }

  selCat.addEventListener('change', buscarLibros);
  selTema.addEventListener('change', buscarLibros);
  inputBuscar.addEventListener('input', buscarConDebounce);

  cargarFiltros();
  buscarLibros();
})();
