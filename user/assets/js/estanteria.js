(function () {
  // window.APP_URL se define desde catalogo.php con tu constante $URL
  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;

  const wrap = document.querySelector('.estante-wrap');
  const fila = document.getElementById('estante');
  const selCat = document.getElementById('f-categoria');
  const selTema = document.getElementById('f-tema');
  const inputBuscar = document.getElementById('f-buscar');

  let libroAbierto = null; // referencia al elemento .libro actualmente centrado

  /* Ancho del lomo según número de páginas (volumen del modelo 3D) */
  function anchoPorPaginas(paginas) {
    const MIN_PAG = 80, MAX_PAG = 900;
    const MIN_ANCHO = 14, MAX_ANCHO = 88;
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

  /* Cierra el libro actualmente centrado y regresa la fila a su lugar */
  function cerrarLibroActual() {
    if (!libroAbierto) return;
    libroAbierto.classList.remove('abierto');
    libroAbierto.querySelector('.libro-inner').style.width = '';
    libroAbierto = null;
    fila.classList.remove('estante-enfocado');
    fila.style.transform = 'translateX(0px)';
  }

  /* Centra el libro clickeado: la fila entera se desplaza para que
     ese libro (ya agrandado y girado hacia la portada) quede en medio
     del contenedor, dando la sensación de que los demás "se abren". */
  function centrarLibro(el, anchoOriginal) {
    // 1. Agranda el libro clickeado (esto empuja a los vecinos, generando el hueco)
    el.classList.add('abierto');
    el.querySelector('.libro-inner').style.width = Math.max(anchoOriginal, 150) + 'px';
    fila.classList.add('estante-enfocado');

    // 2. Espera a que el navegador aplique el nuevo ancho antes de medir posiciones
    requestAnimationFrame(() => {
      const contRect = wrap.getBoundingClientRect();
      const libroRect = el.getBoundingClientRect();
      const centroContenedor = contRect.left + contRect.width / 2;
      const centroLibro = libroRect.left + libroRect.width / 2;
      const offsetActual = parseFloat(fila.dataset.offset || '0');
      const nuevoOffset = offsetActual + (centroContenedor - centroLibro);

      fila.style.transform = `translateX(${nuevoOffset}px)`;
      fila.dataset.offset = nuevoOffset;
    });
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
        <div class="cara canto"></div>
        <div class="cara portada" style="background-image:${fondoPortada};">
          <span class="paginas-tag">${libro.paginas} pág.</span>
          <div class="etiqueta">
            <b>${libro.titulo}</b>
            ${libro.autor}
          </div>
        </div>
      </div>
    `;

    el.addEventListener('click', (ev) => {
      ev.stopPropagation();
      const yaEraEsteAbierto = el === libroAbierto;

      cerrarLibroActual();

      if (!yaEraEsteAbierto) {
        libroAbierto = el;
        centrarLibro(el, ancho);
      }
    });

    return el;
  }

  function pintarEstante(libros) {
    fila.innerHTML = '';
    fila.style.transform = 'translateX(0px)';
    fila.dataset.offset = '0';
    libroAbierto = null;
    if (!libros.length) {
      fila.innerHTML = '<p class="sin-resultados">No se encontraron libros con esos filtros.</p>';
      return;
    }
    libros.forEach(l => fila.appendChild(crearLibroDOM(l)));
  }

  // Click fuera de cualquier libro (pero dentro del estante) cierra el libro centrado
  wrap.addEventListener('click', () => cerrarLibroActual());

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
      fila.innerHTML = '<p class="sin-resultados">Cargando…</p>';
      const res = await fetch(`${API_BUSCAR}?${params.toString()}`);
      const libros = await res.json();
      pintarEstante(libros);
    } catch (err) {
      console.error('Error buscando libros', err);
      fila.innerHTML = '<p class="sin-resultados">Ocurrió un error cargando los libros.</p>';
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
