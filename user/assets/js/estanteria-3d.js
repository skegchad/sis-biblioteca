import * as THREE from 'three';

(async function () {
  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;

  const contenedorPrincipal = document.getElementById('estantes-categorias');
  const cargandoGlobal = document.getElementById('cargando-estantes');
  const sinResultados = document.getElementById('sin-resultados');

  const selCat = document.getElementById('f-categoria');
  const selSubcat = document.getElementById('f-subcategoria');
  const selTipo = document.getElementById('f-tipo');
  const selIdioma = document.getElementById('f-idioma');
  const selDisponibilidad = document.getElementById('f-disponibilidad');
  const inputBuscar = document.getElementById('f-buscar');

  const botonFiltros = document.getElementById('boton-filtros');
  const panelFiltros = document.getElementById('panel-filtros');
  const cerrarFiltros = document.getElementById('cerrar-filtros');

  const abrirTemas = document.getElementById('abrir-temas');
  const panelTemas = document.getElementById('panel-temas');
  const cerrarTemas = document.getElementById('cerrar-temas');
  const aceptarTemas = document.getElementById('aceptar-temas');
  const buscarTema = document.getElementById('buscar-tema');
  const listaTemas = document.getElementById('lista-temas');

  const btnLimpiarFiltros = document.getElementById('limpiar-filtros');

  // Panel de información GLOBAL (uno solo para toda la página)
  const infoLibro = document.getElementById('info-libro');
  const infoPortada = document.getElementById('info-portada');
  const infoTitulo = document.getElementById('info-titulo');
  const infoAutor = document.getElementById('info-autor');
  const infoDescripcion = document.getElementById('info-descripcion');
  const infoPaginas = document.getElementById('info-paginas');
  const infoCategoria = document.getElementById('info-categoria');
  const infoTema = document.getElementById('info-tema');
  const infoTipo = document.getElementById('info-tipo');
  const infoIdioma = document.getElementById('info-idioma');
  const infoEdicion = document.getElementById('info-edicion');
  const infoEjemplares = document.getElementById('info-ejemplares');
  const infoPrestados = document.getElementById('info-prestados');
  const infoDisponibilidad = document.getElementById('info-disponibilidad');
  const botonLeerLibro = document.getElementById('boton-leer-libro');

  let datosFiltros = { categorias: [], subcategorias: [], tipos: [], idiomas: [], temas: [] };
  let temasSeleccionados = [];
  let temasTemporales = [];

  const ALTO_LIBRO = 1.55;
  const PROFUNDIDAD_LIBRO = 1.0;
  const SEPARACION = 0.02;
  const Z_SELECCIONADO = 2;
  const SUAVIDAD_DESPLAZAMIENTO = 0.12;
  const MARGEN_APERTURA = 1.25;

  // Selección global: solo un libro puede estar abierto en toda la página
  let seleccionGlobal = null; // { estante, malla }

  const animacionesLibros = new WeakMap();
  function detenerAnimacionLibro(malla) {
    const animacion = animacionesLibros.get(malla);
    if (animacion) {
      animacion.cancelado = true;
      animacionesLibros.delete(malla);
    }
  }

  // ============================================================
  // Utilidades de color / textura (sin cambios respecto a la versión anterior)
  // ============================================================
  function colorTexto(hex) {
    const c = hex.replace('#', '');
    const r = parseInt(c.substr(0, 2), 16), g = parseInt(c.substr(2, 2), 16), b = parseInt(c.substr(4, 2), 16);
    const luminancia = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminancia > 0.55 ? '#20201c' : '#f7f4ec';
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

  function grosorPorPaginas(paginas) {
    const MIN_PAG = 80, MAX_PAG = 900, MIN_GROSOR = 0.08, MAX_GROSOR = 0.52;
    const p = Math.max(MIN_PAG, Math.min(MAX_PAG, paginas));
    const t = (p - MIN_PAG) / (MAX_PAG - MIN_PAG);
    return MIN_GROSOR + t * (MAX_GROSOR - MIN_GROSOR);
  }

  const ESCALA_LOMO = 900;
  function texturaLomo(libro, grosor) {
    const anchoPx = Math.max(48, Math.round(grosor * ESCALA_LOMO));
    const altoPx = Math.round(ALTO_LIBRO * ESCALA_LOMO);
    const canvas = document.createElement('canvas');
    canvas.width = anchoPx;
    canvas.height = altoPx;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = libro.color;
    ctx.fillRect(0, 0, anchoPx, altoPx);

    const fontPx = Math.round(0.085 * ESCALA_LOMO);
    ctx.fillStyle = colorTexto(libro.color);
    ctx.font = `500 ${fontPx}px 'Fraunces', serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.save();
    ctx.translate(anchoPx / 2, altoPx / 2);
    ctx.rotate(Math.PI / 2);
    let titulo = libro.titulo;
    const maxAncho = altoPx - fontPx * 1.6;
    while (ctx.measureText(titulo).width > maxAncho && titulo.length > 3) {
      titulo = titulo.slice(0, -2);
    }
    if (titulo !== libro.titulo) titulo += '…';
    ctx.fillText(titulo, 0, 0);
    ctx.restore();

    const tex = new THREE.CanvasTexture(canvas);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
  }

  function envolverTexto(ctx, texto, x, y, anchoMax, alturaLinea) {
    const palabras = texto.split(' ');
    let linea = '';
    const lineas = [];
    for (const palabra of palabras) {
      const prueba = linea ? `${linea} ${palabra}` : palabra;
      if (ctx.measureText(prueba).width > anchoMax && linea) {
        lineas.push(linea);
        linea = palabra;
      } else {
        linea = prueba;
      }
    }
    lineas.push(linea);
    const inicioY = y - (lineas.length - 1) * alturaLinea;
    lineas.slice(0, 2).forEach((l, i) => ctx.fillText(l, x, inicioY + i * alturaLinea));
  }

  const ESCALA_PORTADA = 700;
  function texturaPortada(libro) {
    const anchoPx = Math.round(PROFUNDIDAD_LIBRO * ESCALA_PORTADA);
    const altoPx = Math.round(ALTO_LIBRO * ESCALA_PORTADA);
    return new Promise((resolve) => {
      const canvas = document.createElement('canvas');
      canvas.width = anchoPx;
      canvas.height = altoPx;
      const ctx = canvas.getContext('2d');

      function fondoDegradado() {
        const g = ctx.createLinearGradient(0, 0, anchoPx, altoPx);
        g.addColorStop(0, libro.color);
        g.addColorStop(1, sombrear(libro.color, -18));
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, anchoPx, altoPx);
      }

      function dibujarEtiquetaYResolver() {
        const degradado = ctx.createLinearGradient(0, altoPx * 0.55, 0, altoPx);
        degradado.addColorStop(0, 'rgba(0,0,0,0)');
        degradado.addColorStop(1, 'rgba(0,0,0,0.78)');
        ctx.fillStyle = degradado;
        ctx.fillRect(0, altoPx * 0.55, anchoPx, altoPx * 0.45);

        const margenPx = Math.round(0.05 * ESCALA_PORTADA);
        const fontTituloPx = Math.round(0.058 * ESCALA_PORTADA);
        const fontAutorPx = Math.round(0.04 * ESCALA_PORTADA);

        ctx.fillStyle = '#fff';
        ctx.textAlign = 'left';
        ctx.font = `500 ${fontTituloPx}px 'Fraunces', serif`;
        envolverTexto(ctx, libro.titulo, margenPx, altoPx - fontAutorPx * 2.3, anchoPx - margenPx * 2, fontTituloPx * 1.2);

        ctx.font = `400 ${fontAutorPx}px 'Inter', sans-serif`;
        ctx.fillText(libro.autor, margenPx, altoPx - margenPx * 0.9);

        const tex = new THREE.CanvasTexture(canvas);
        tex.colorSpace = THREE.SRGBColorSpace;
        resolve(tex);
      }

      if (libro.rutaFotoAbsoluta) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
          const escala = Math.max(anchoPx / img.width, altoPx / img.height);
          const w = img.width * escala, h = img.height * escala;
          ctx.drawImage(img, (anchoPx - w) / 2, (altoPx - h) / 2, w, h);
          dibujarEtiquetaYResolver();
        };
        img.onerror = () => { fondoDegradado(); dibujarEtiquetaYResolver(); };
        img.src = libro.rutaFotoAbsoluta;
      } else {
        fondoDegradado();
        dibujarEtiquetaYResolver();
      }
    });
  }

  // Textura de canto de páginas: compartida entre TODOS los estantes/libros
  const materialPaginas = new THREE.MeshBasicMaterial({
    map: (function () {
      const canvas = document.createElement('canvas');
      canvas.width = 64;
      canvas.height = 64;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#f4efe0';
      ctx.fillRect(0, 0, 64, 64);
      ctx.strokeStyle = '#e4dcc4';
      ctx.lineWidth = 1;
      for (let x = 0; x < 64; x += 3) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, 64);
        ctx.stroke();
      }
      return new THREE.CanvasTexture(canvas);
    })(),
  });

  async function crearLibro3D(libro) {
    const grosor = grosorPorPaginas(libro.paginas);
    const geometria = new THREE.BoxGeometry(grosor, ALTO_LIBRO, PROFUNDIDAD_LIBRO);

    const [texLomo, texPortada] = await Promise.all([
      Promise.resolve(texturaLomo(libro, grosor)),
      texturaPortada(libro),
    ]);

    const materialLomo = new THREE.MeshBasicMaterial({ map: texLomo });
    const materialPortada = new THREE.MeshBasicMaterial({ map: texPortada });

    const materiales = [
      materialPortada, // +x portada
      materialPaginas, // -x
      materialPaginas, // +y
      materialPaginas, // -y
      materialLomo,    // +z lomo
      materialPaginas, // -z
    ];

    const malla = new THREE.Mesh(geometria, materiales);
    malla.position.y = ALTO_LIBRO / 2;
    malla.userData = { libro, grosor, abierto: false, posXBase: 0, posZBase: 0 };
    return malla;
  }

  // Tween genérico (ease-out cúbico)
  function animarLibro(malla, destino, duracion = 600, alFinal = null) {
    detenerAnimacionLibro(malla);

    const inicio = {
      x: malla.position.x,
      z: malla.position.z,
      rotY: malla.rotation.y,
      opacidad: malla.material[0].opacity ?? 1,
    };

    const animacion = { cancelado: false };
    animacionesLibros.set(malla, animacion);
    const t0 = performance.now();

    function tick(ahora) {
      if (animacion.cancelado) return;
      const t = Math.min(1, (ahora - t0) / duracion);
      const suavizado = 1 - Math.pow(1 - t, 3);

      malla.position.x = inicio.x + (destino.x - inicio.x) * suavizado;
      malla.position.z = inicio.z + (destino.z - inicio.z) * suavizado;
      malla.rotation.y = inicio.rotY + (destino.rotY - inicio.rotY) * suavizado;

      const opacidad = inicio.opacidad + (destino.opacidad - inicio.opacidad) * suavizado;
      malla.material.forEach((mat) => { mat.transparent = true; mat.opacity = opacidad; });

      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        animacionesLibros.delete(malla);
        if (alFinal) alFinal();
      }
    }
    requestAnimationFrame(tick);
  }

  // ============================================================
  // CLASE Estante: un canvas/escena/cámara/renderer independiente
  // por categoría, con título propio encima.
  // ============================================================
  class Estante {
    constructor(categoria) {
      this._tokenSeleccion = 0;
      this.id = categoria.id;
      this.nombre = categoria.nombre;
      this.datosLibros = [];

      this.libros = [];        // mallas 3D, solo existen si renderer está vivo
      this.anchoFila = 1;
      this.desplazamientoX = 0;
      this.desplazamientoVisualX = 0;
      this.arrastrando = false;
      this.huboArrastre = false;
      this.inicioArrastreX = 0;
      this.desplazamientoInicioX = 0;
      this.libroSeleccionado = null;

      this.escena = null;
      this.camara = null;
      this.renderer = null;

      this._resizeObs = null;

      this._crearDOM();
    }

    _crearDOM() {
      const seccion = document.createElement('section');
      seccion.className = 'estante-categoria';
      seccion.dataset.categoriaId = this.id;

      const titulo = document.createElement('h3');
      titulo.className = 'titulo-estante';
      this._tituloEl = titulo;
      this._actualizarTitulo(0);

      const lienzo = document.createElement('div');
      lienzo.className = 'lienzo-estante';

      const cargando = document.createElement('div');
      cargando.className = 'cargando-estante';
      cargando.innerHTML = `<div class="spinner"></div><span>Cargando libros...</span>`;
      this.cargandoEl = cargando;

      const mensajeVacio = document.createElement('div');
      mensajeVacio.className = 'estante-vacio';
      mensajeVacio.textContent = 'No hay libros de esta categoría';
      mensajeVacio.style.display = 'none';

      const botonInfo = document.createElement('button');
      botonInfo.type = 'button';
      botonInfo.className = 'boton-info-libro';
      botonInfo.setAttribute('aria-label', 'Ver información');
      botonInfo.textContent = '˅';
      botonInfo.style.display = 'none';

      const controles = document.createElement('div');
      controles.className = 'controles-libro';
      controles.style.display = 'none';
      const btnAnt = document.createElement('button');
      btnAnt.type = 'button';
      btnAnt.className = 'control-libro';
      btnAnt.setAttribute('aria-label', 'Libro anterior');
      btnAnt.textContent = '◀';
      const btnSig = document.createElement('button');
      btnSig.type = 'button';
      btnSig.className = 'control-libro';
      btnSig.setAttribute('aria-label', 'Libro siguiente');
      btnSig.textContent = '▶';
      controles.append(btnAnt, btnSig);

      // <-- el append va DESPUÉS de declarar todo lo anterior
      lienzo.append(mensajeVacio, cargando, botonInfo, controles);
      seccion.append(titulo, lienzo);
      contenedorPrincipal.appendChild(seccion);

      this.seccionEl = seccion;
      this.lienzoEl = lienzo;
      this.mensajeVacioEl = mensajeVacio;
      this.botonInfoEl = botonInfo;
      this.controlesEl = controles;
      this.btnAnteriorEl = btnAnt;
      this.btnSiguienteEl = btnSig;

      btnAnt.addEventListener('click', (ev) => { ev.stopPropagation(); this.seleccionarAdyacente(-1); });
      btnSig.addEventListener('click', (ev) => { ev.stopPropagation(); this.seleccionarAdyacente(1); });
      botonInfo.addEventListener('click', (ev) => {
        ev.stopPropagation();
        if (!this.libroSeleccionado) return;
        if (infoLibro.classList.contains('visible')) {
          infoLibro.classList.remove('visible');
          botonInfo.textContent = '˅';
          botonInfo.setAttribute('aria-label', 'Ver información');
        } else {
          mostrarInformacionLibro(this.libroSeleccionado.userData.libro);
          botonInfo.textContent = '⌃';
          botonInfo.setAttribute('aria-label', 'Cerrar información');
        }
      });
    }

    _actualizarTitulo(cantidad) {
      this._tituloEl.textContent = `${this.nombre} (${cantidad})`;
    }

    // -------------------- Ciclo de vida del renderer --------------------

    async inicializar() {
      if (this.renderer) return;

      this.escena = new THREE.Scene();
      this.escena.background = new THREE.Color(0xede7da);

      this.camara = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 100);
      this.camara.position.set(0, ALTO_LIBRO / 2, 5);

      this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
      this.lienzoEl.prepend(this.renderer.domElement);
      this.renderer.domElement.classList.add('canvas-estante');

      this._sincronizarTamano();
      this._resizeObs = new ResizeObserver(() => this._sincronizarTamano());
      this._resizeObs.observe(this.lienzoEl);

      this._adjuntarEventos();

      await this._pintar(this.datosLibros);
    }

    destruir() {
      if (!this.renderer) return;

      if (seleccionGlobal && seleccionGlobal.estante === this) {
        seleccionGlobal = null;
        infoLibro.classList.remove('visible');
      }

      this._tokenSeleccion++;

      this.libros.forEach((malla) => {
        detenerAnimacionLibro(malla);
        this.escena.remove(malla);
        malla.geometry.dispose();
        malla.material.forEach((mat) => { if (mat.map) mat.map.dispose(); mat.dispose(); });
      });
      this.libros = [];
      this.libroSeleccionado = null;
      this.controlesEl.style.display = 'none';
      this.botonInfoEl.style.display = 'none';

      if (this._resizeObs) this._resizeObs.disconnect();

      this.renderer.dispose();
      this.renderer.domElement.remove();
      this.renderer = null;
      this.escena = null;
      this.camara = null;
    }

    _sincronizarTamano() {
      if (!this.renderer) return;
      const ancho = this.lienzoEl.clientWidth;
      const alto = this.lienzoEl.clientHeight;
      if (!ancho || !alto) return;
      this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
      this.renderer.setSize(ancho, alto, false);
      this._ajustarCamara();
    }

    _ajustarCamara() {
      const aspecto = this.lienzoEl.clientWidth / this.lienzoEl.clientHeight;
      const esMovil = window.matchMedia('(max-width: 768px)').matches;
      const alturaFrustum = esMovil ? 1.6 : 1.58;
      const anchoFrustum = alturaFrustum * aspecto;

      this.camara.left = -anchoFrustum / 2;
      this.camara.right = anchoFrustum / 2;
      this.camara.top = alturaFrustum / 2;
      this.camara.bottom = -alturaFrustum / 2;
      this.camara.updateProjectionMatrix();
    }

    // -------------------- Contenido --------------------

    async establecerLibros(libros) {
      this.datosLibros = libros;
      this._actualizarTitulo(libros.length);
      if (this.renderer) {
        await this._pintar(libros);
      }
    }

    async _pintar(libros) {
      // Mostrar spinner ANTES de tocar nada
      this.cargandoEl.classList.remove('oculto');

      // Limpiar libros anteriores
      this.libros.forEach((malla) => {
        this.escena.remove(malla);
        malla.geometry.dispose();
        malla.material.forEach((mat) => { if (mat.map) mat.map.dispose(); mat.dispose(); });
      });
      this.libros = [];
      this.libroSeleccionado = null;
      this.desplazamientoX = 0;
      this.desplazamientoVisualX = 0;

      if (libros.length === 0) {
        this.mensajeVacioEl.style.display = 'flex';
        this.anchoFila = 1;
        this.cargandoEl.classList.add('oculto'); // <- ocultar también en caso vacío
        return;
      }
      this.mensajeVacioEl.style.display = 'none';

      for (const libro of libros) {
        const rutaFotoAbsoluta = libro.ruta_foto
          ? `${BASE}/${libro.ruta_foto}`.replace(/([^:]\/)\/+/g, '$1')
          : null;
        const malla = await crearLibro3D({ ...libro, rutaFotoAbsoluta });
        this.escena.add(malla);
        this.libros.push(malla);
      }

      this._acomodarEnFila();

      // Todo listo: ocultar spinner
      this.cargandoEl.classList.add('oculto');
    }

    _acomodarEnFila() {
      const anchoTotal = this.libros.reduce((acc, m) => acc + m.userData.grosor + SEPARACION, 0);
      this.anchoFila = anchoTotal || 1;

      let x = -anchoTotal / 2;
      this.libros.forEach((malla) => {
        const g = malla.userData.grosor;
        const posX = x + g / 2;
        malla.userData.posXBase = posX;
        malla.userData.posZBase = 0;
        malla.position.x = posX;
        malla.position.z = 0;
        x += g + SEPARACION;
      });
    }

    // -------------------- Interacción --------------------

    _limiteDesplazamiento() {
      const anchoVisible = this.camara.right - this.camara.left;
      const exceso = this.anchoFila - anchoVisible;
      return Math.max(0, exceso / 2);
    }

    _aplicarDesplazamiento() {
      if (this.libroSeleccionado) return;
      this.libros.forEach((malla) => {
        malla.position.x = malla.userData.posXBase + this.desplazamientoVisualX;
      });
    }

    _adjuntarEventos() {
      const canvas = this.renderer.domElement;
      const raycaster = new THREE.Raycaster();
      const puntero = new THREE.Vector2();

      canvas.addEventListener('mousemove', (ev) => {
        if (this.arrastrando) { canvas.style.cursor = 'grabbing'; return; }
        const rect = canvas.getBoundingClientRect();
        puntero.x = ((ev.clientX - rect.left) / rect.width) * 2 - 1;
        puntero.y = -((ev.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(puntero, this.camara);
        const hits = raycaster.intersectObjects(this.libros);
        canvas.style.cursor = hits.length > 0 ? 'pointer' : 'default';
      });

      canvas.addEventListener('pointerdown', (ev) => {
        if (this.libroSeleccionado) return;
        this.arrastrando = true;
        this.huboArrastre = false;
        this.inicioArrastreX = ev.clientX;
        this.desplazamientoInicioX = this.desplazamientoX;
        canvas.setPointerCapture(ev.pointerId);
        canvas.style.cursor = 'grabbing';
      });

      canvas.addEventListener('pointermove', (ev) => {
        if (!this.arrastrando) return;
        const diferencia = ev.clientX - this.inicioArrastreX;
        if (Math.abs(diferencia) > 5) this.huboArrastre = true;
        const limite = this._limiteDesplazamiento();
        this.desplazamientoX = Math.max(-limite, Math.min(limite, this.desplazamientoInicioX + diferencia));
        this._aplicarDesplazamiento();
      });

      canvas.addEventListener('pointerup', (ev) => {
        if (!this.arrastrando) return;
        this.arrastrando = false;
        canvas.releasePointerCapture(ev.pointerId);
        canvas.style.cursor = 'default';
      });

      canvas.addEventListener('click', (ev) => {
        if (this.huboArrastre) { this.huboArrastre = false; return; }

        const rect = canvas.getBoundingClientRect();
        puntero.x = ((ev.clientX - rect.left) / rect.width) * 2 - 1;
        puntero.y = -((ev.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(puntero, this.camara);
        const hits = raycaster.intersectObjects(this.libros);

        if (!hits.length) { this.cerrarSeleccionado(); return; }

        const malla = hits[0].object;
        malla === this.libroSeleccionado ? this.cerrarSeleccionado() : this.seleccionar(malla);
      });
    }

    seleccionar(malla) {
      if (malla === this.libroSeleccionado) return;

      if (seleccionGlobal && seleccionGlobal.malla !== malla) {
        seleccionGlobal.estante.cerrarSeleccionado();
      }

      // Invalida cualquier cierre pendiente que quedó de una selección anterior
      this._tokenSeleccion++;

      this.desplazamientoX = 0;
      this.desplazamientoVisualX = 0;
      this.libroSeleccionado = malla;
      seleccionGlobal = { estante: this, malla };

      this.botonInfoEl.style.display = 'block';
      this.botonInfoEl.textContent = '˅';
      this.botonInfoEl.setAttribute('aria-label', 'Ver información');

      malla.userData.abierto = true;

      const indice = this.libros.indexOf(malla);

      animarLibro(malla, { x: 0, z: Z_SELECCIONADO, rotY: -Math.PI / 2, opacidad: 1 }, 600);

      if (indice > 0) {
        const izquierda = this.libros[indice - 1];
        const nuevoX = -(malla.userData.grosor / 2) - MARGEN_APERTURA - (izquierda.userData.grosor / 2);
        const desplazamiento = nuevoX - izquierda.userData.posXBase;
        for (let i = 0; i < indice; i++) {
          const libro = this.libros[i];
          animarLibro(libro, { x: libro.userData.posXBase + desplazamiento, z: libro.userData.posZBase, rotY: 0, opacidad: 0.35 }, 600);
        }
      }

      if (indice < this.libros.length - 1) {
        const derecha = this.libros[indice + 1];
        const nuevoX = (malla.userData.grosor / 2) + MARGEN_APERTURA + (derecha.userData.grosor / 2);
        const desplazamiento = nuevoX - derecha.userData.posXBase;
        for (let i = indice + 1; i < this.libros.length; i++) {
          const libro = this.libros[i];
          animarLibro(libro, { x: libro.userData.posXBase + desplazamiento, z: libro.userData.posZBase, rotY: 0, opacidad: 0.35 }, 600);
        }
      }

      this._actualizarControles();
    }

    cerrarSeleccionado() {
      if (!this.libroSeleccionado) return;

      infoLibro.classList.remove('visible');
      const mallaSeleccionada = this.libroSeleccionado;
      mallaSeleccionada.userData.abierto = false;

      this.libros.forEach((malla) => {
        animarLibro(malla, { x: malla.userData.posXBase, z: malla.userData.posZBase, rotY: 0, opacidad: 1 }, 600);
      });

      this.desplazamientoX = 0;
      this.desplazamientoVisualX = 0;

      // Token de esta "generación" de cierre — si en el ínterin se selecciona
      // otro libro (o el mismo vuelve a abrirse), el token cambia y este
      // timeout ya no debe limpiar nada.
      this._tokenSeleccion++;
      const tokenAlCerrar = this._tokenSeleccion;

      setTimeout(() => {
        if (this._tokenSeleccion === tokenAlCerrar) {
          this.libroSeleccionado = null;
          if (seleccionGlobal && seleccionGlobal.malla === mallaSeleccionada) seleccionGlobal = null;
          this.botonInfoEl.style.display = 'none';
          this._actualizarControles();
        }
      }, 620);
    }

    seleccionarAdyacente(direccion) {
      if (!this.libroSeleccionado) return;
      const indice = this.libros.indexOf(this.libroSeleccionado);
      if (indice === -1) return;
      const nuevoIndice = indice + direccion;
      if (nuevoIndice < 0 || nuevoIndice >= this.libros.length) return;
      this.seleccionar(this.libros[nuevoIndice]);
    }

    _actualizarControles() {
      if (!this.libroSeleccionado) {
        this.controlesEl.style.display = 'none';
        return;
      }
      this.controlesEl.style.display = 'flex';
      const indice = this.libros.indexOf(this.libroSeleccionado);
      this.btnAnteriorEl.disabled = indice <= 0;
      this.btnSiguienteEl.disabled = indice >= this.libros.length - 1;
    }

    // Posiciona el botón de info flotando sobre la esquina del libro abierto
    actualizarBotonInfoPosicion() {
      if (!this.libroSeleccionado || !this.renderer) return;
      const caja = new THREE.Box3().setFromObject(this.libroSeleccionado);
      const esquina = new THREE.Vector3(caja.min.x, caja.max.y, caja.max.z);
      esquina.project(this.camara);

      const rect = this.renderer.domElement.getBoundingClientRect();
      const x = ((esquina.x + 1) / 2) * rect.width;
      const y = ((-esquina.y + 1) / 2) * rect.height;

      this.botonInfoEl.style.left = `${x - 8}px`;
      this.botonInfoEl.style.top = `${y - 8}px`;
    }

    animarDesplazamientoSuave() {
      if (!this.libroSeleccionado) {
        this.desplazamientoVisualX += (this.desplazamientoX - this.desplazamientoVisualX) * SUAVIDAD_DESPLAZAMIENTO;
        this._aplicarDesplazamiento();
      }
    }
  }

  // ============================================================
  // Gestión global de estantes + lazy loading con IntersectionObserver
  // ============================================================
  let estantes = [];          // estantes de categoría (como ya tenías)
let estanteResultados = null; // estante especial para modo búsqueda/filtro
let modoActual = 'categorias'; // 'categorias' | 'resultados'

  const observadorVisibilidad = new IntersectionObserver(
    (entradas) => {
      entradas.forEach((entrada) => {
        const estante = estantes.find((e) => e.seccionEl === entrada.target);
        if (!estante) return;
        if (entrada.isIntersecting) {
          estante.inicializar();
        } else {
          estante.destruir();
        }
      });
    },
    { root: null, rootMargin: '600px 0px', threshold: 0 }
  );

  function crearEstantesCategorias(categorias) {
  estantes.forEach((estante) => {
    observadorVisibilidad.unobserve(estante.seccionEl);
    estante.destruir();
    estante.seccionEl.remove();
  });
  estantes = [];

  categorias.forEach((categoria) => {
    const estante = new Estante(categoria);
    estantes.push(estante);
    observadorVisibilidad.observe(estante.seccionEl);
  });

  construirTabsCategorias();
}

  function construirTabsCategorias() {
    const cont = document.getElementById('tabs-categorias');
    if (!cont) return;
    cont.innerHTML = '';

    estantes.forEach((estante) => {
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'tab-categoria';
      boton.dataset.categoriaId = estante.id;
      boton.textContent = `${estante.nombre} (${estante.datosLibros.length})`;
      boton.addEventListener('click', () => {
        estante.seccionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
      cont.appendChild(boton);
    });
  }

  // ============================================================
  // Loop de render único: recorre solo los estantes con renderer activo
  // ============================================================
  (function animar() {
  requestAnimationFrame(animar);

  const activos = estanteResultados ? [...estantes, estanteResultados] : estantes;

  activos.forEach((estante) => {
    if (!estante.renderer) return;
    estante.animarDesplazamientoSuave();
    estante.actualizarBotonInfoPosicion();
    estante.renderer.render(estante.escena, estante.camara);
  });
})();

  // ============================================================
  // Panel de información del libro (global)
  // ============================================================
  function mostrarInformacionLibro(libro) {
    if (!libro) return;

    infoTitulo.textContent = libro.titulo || 'Sin título';
    infoAutor.textContent = libro.autor || 'Autor desconocido';
    infoDescripcion.textContent = libro.descripcion || 'Sin descripción';

    if (libro.ruta_foto) {
      infoPortada.src = `${BASE}/${libro.ruta_foto}`.replace(/([^:]\/)\/+/g, '$1');
      infoPortada.alt = `Portada de ${libro.titulo || 'libro'}`;
    } else {
      infoPortada.src = '';
      infoPortada.alt = 'Portada del libro';
    }

    infoPaginas.textContent = libro.paginas || '—';
    infoCategoria.textContent = libro.categoria || '—';
    infoTema.textContent = libro.temas || '—';
    infoTipo.textContent = libro.tipo || '—';
    infoIdioma.textContent = libro.idioma || '—';
    infoEdicion.textContent = libro.edicion || '—';

    const ejemplares = Number(libro.ejemplares) || 0;
    const prestados = Number(libro.prestados) || 0;
    infoEjemplares.textContent = ejemplares;
    infoPrestados.textContent = prestados;

    if (ejemplares > prestados) {
      infoDisponibilidad.textContent = 'Disponible';
      infoDisponibilidad.classList.remove('prestado');
      infoDisponibilidad.classList.add('disponible');
    } else {
      infoDisponibilidad.textContent = 'No disponible';
      infoDisponibilidad.classList.remove('disponible');
      infoDisponibilidad.classList.add('prestado');
    }

    if (libro.ruta_pdf) {
      botonLeerLibro.href = `${BASE}/${libro.ruta_pdf}`.replace(/([^:]\/)\/+/g, '$1');
      botonLeerLibro.style.display = 'flex';
    } else {
      botonLeerLibro.removeAttribute('href');
      botonLeerLibro.style.display = 'none';
    }

    infoLibro.classList.add('visible');
  }

  document.addEventListener('keydown', (ev) => {
    if (!seleccionGlobal) return;
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(ev.target.tagName)) return;
    if (ev.key === 'ArrowLeft') { ev.preventDefault(); seleccionGlobal.estante.seleccionarAdyacente(-1); }
    else if (ev.key === 'ArrowRight') { ev.preventDefault(); seleccionGlobal.estante.seleccionarAdyacente(1); }
  });

  // ============================================================
  // Filtros y búsqueda
  // ============================================================
  async function cargarFiltros() {
    try {
      const res = await fetch(API_FILTROS);
      const data = await res.json();
      datosFiltros = data;

      selCat.innerHTML = '<option value="">Todas las categorías</option>' +
        data.categorias.map((c) => `<option value="${c.nombre}">${c.nombre}</option>`).join('');

      selTipo.innerHTML = '<option value="">Todos los tipos</option>' +
        data.tipos.map((t) => `<option value="${t.nombre}">${t.nombre}</option>`).join('');

      selIdioma.innerHTML = '<option value="">Todos los idiomas</option>' +
        data.idiomas.map((i) => `<option value="${i}">${i}</option>`).join('');

      actualizarSubcategorias();
      dibujarTemas();
    } catch (err) {
      console.error('No se pudieron cargar los filtros', err);
    }
  }

  function actualizarSubcategorias() {
    const categoriaNombre = selCat.value;
    if (!categoriaNombre) {
      selSubcat.innerHTML = '<option value="">Todas las subcategorías</option>';
      selSubcat.disabled = true;
      return;
    }
    const categoria = datosFiltros.categorias.find((c) => c.nombre === categoriaNombre);
    if (!categoria) {
      selSubcat.innerHTML = '<option value="">Todas las subcategorías</option>';
      selSubcat.disabled = true;
      return;
    }
    const subcategorias = datosFiltros.subcategorias.filter((s) => Number(s.categoria_id) === Number(categoria.id));
    selSubcat.disabled = subcategorias.length === 0;
    selSubcat.innerHTML = '<option value="">Todas las subcategorías</option>' +
      subcategorias.map((s) => `<option value="${s.nombre}">${s.nombre}</option>`).join('');
  }

  function dibujarTemas() {
    const texto = buscarTema.value.trim().toLowerCase();
    const temasFiltrados = datosFiltros.temas.filter((tema) => tema.nombre.toLowerCase().includes(texto));
    listaTemas.innerHTML = '';

    temasFiltrados.forEach((tema) => {
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'tema-chip';
      boton.textContent = tema.nombre;
      if (temasTemporales.includes(Number(tema.id))) boton.classList.add('seleccionado');

      boton.addEventListener('click', () => {
        const id = Number(tema.id);
        temasTemporales = temasTemporales.includes(id)
          ? temasTemporales.filter((t) => t !== id)
          : [...temasTemporales, id];
        dibujarTemas();
      });

      listaTemas.appendChild(boton);
    });
  }

  function actualizarTextoTemas() {
    const contenedor = document.getElementById('temas-seleccionados');
    if (!contenedor) return;
    contenedor.innerHTML = '';

    if (temasSeleccionados.length === 0) {
      contenedor.textContent = 'Ningún tema seleccionado';
      return;
    }

    temasSeleccionados.forEach((id) => {
      const tema = datosFiltros.temas.find((t) => Number(t.id) === Number(id));
      if (!tema) return;
      const chip = document.createElement('span');
      chip.className = 'tema-chip seleccionado';
      chip.textContent = tema.nombre;
      contenedor.appendChild(chip);
    });
  }

  async function buscarLibros() {
    mostrarCarga();

    const params = new URLSearchParams({
      categoria: selCat.value || '',
      subcategoria: selSubcat.value || '',
      tipo: selTipo.value || '',
      idioma: selIdioma.value || '',
      disponibilidad: selDisponibilidad.value || '',
      buscar: inputBuscar.value.trim() || '',
    });
    temasSeleccionados.forEach((id) => params.append('temas[]', id));

    try {
      const res = await fetch(`${API_BUSCAR}?${params.toString()}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const libros = await res.json();

      // Si aún no existen los estantes de categoría (primera carga), crearlos
      if (estantes.length === 0) {
        crearEstantesCategorias(datosFiltros.categorias);
      }

      if (hayFiltrosActivos()) {
        await mostrarModoResultados();
        await estanteResultados.establecerLibros(libros);
      } else {
        mostrarModoCategorias();

        const grupos = {};
        libros.forEach((libro) => {
          const categoria = libro.categoria || 'Sin categoría';
          if (!grupos[categoria]) grupos[categoria] = [];
          grupos[categoria].push(libro);
        });

        for (const estante of estantes) {
          const librosCategoria = grupos[estante.nombre] || [];
          await estante.establecerLibros(librosCategoria);
        }

        construirTabsCategorias();
      }

      sinResultados.style.display = libros.length === 0 ? 'block' : 'none';
      sinResultados.textContent = 'No se encontraron libros.';

      ocultarCarga();
    } catch (err) {
      console.error('Error buscando libros', err);
      sinResultados.textContent = 'Ocurrió un error cargando los libros.';
      sinResultados.style.display = 'block';
      ocultarCarga();
    }
  }

  function limpiarTemas() {
    temasSeleccionados = [];
    actualizarTextoTemas();
  }

  function limpiarFiltros() {
    selCat.value = '';
    selSubcat.value = '';
    selTipo.value = '';
    selIdioma.value = '';
    selDisponibilidad.value = '';
    inputBuscar.value = '';
    limpiarTemas();
    buscarLibros();
  }

  function mostrarCarga() {
  cargandoGlobal.classList.remove('oculto');

  estantes.forEach((estante) => {
    if (estante.cargandoEl) estante.cargandoEl.classList.remove('oculto');
  });
  if (estanteResultados && estanteResultados.cargandoEl) {
    estanteResultados.cargandoEl.classList.remove('oculto');
  }
}

function ocultarCarga() {
  cargandoGlobal.classList.add('oculto');
}

  btnLimpiarFiltros.addEventListener('click', limpiarFiltros);

  let temporizador;
  function buscarConDebounce() {
    clearTimeout(temporizador);
    temporizador = setTimeout(buscarLibros, 300);
  }

  selCat.addEventListener('change', () => { actualizarSubcategorias(); buscarLibros(); });
  selSubcat.addEventListener('change', buscarLibros);
  selTipo.addEventListener('change', buscarLibros);
  selIdioma.addEventListener('change', buscarLibros);
  selDisponibilidad.addEventListener('change', buscarLibros);
  inputBuscar.addEventListener('input', buscarConDebounce);

  buscarTema.addEventListener('input', dibujarTemas);

  botonFiltros.addEventListener('click', () => panelFiltros.classList.add('abierto'));
  cerrarFiltros.addEventListener('click', () => panelFiltros.classList.remove('abierto'));

  abrirTemas.addEventListener('click', () => {
    temasTemporales = [...temasSeleccionados];
    buscarTema.value = '';
    dibujarTemas();
    panelTemas.classList.add('abierto');
  });
  aceptarTemas.addEventListener('click', () => {
    temasSeleccionados = [...temasTemporales];
    actualizarTextoTemas();
    panelTemas.classList.remove('abierto');
    buscarLibros();
  });
  cerrarTemas.addEventListener('click', () => panelTemas.classList.remove('abierto'));

  // ============================================================
  // Arranque
  // ============================================================
  await cargarFiltros();
  crearEstantesCategorias(datosFiltros.categorias);
  await buscarLibros();

  function hayFiltrosActivos() {
  return !!(
    selCat.value ||
    selSubcat.value ||
    selTipo.value ||
    selIdioma.value ||
    selDisponibilidad.value ||
    inputBuscar.value.trim() ||
    temasSeleccionados.length > 0
  );
}
function mostrarModoCategorias() {
  if (modoActual === 'categorias') return;
  modoActual = 'categorias';

  if (estanteResultados) {
    estanteResultados.destruir();
    estanteResultados.seccionEl.style.display = 'none';
  }

  estantes.forEach((estante) => {
    estante.seccionEl.style.display = '';
    observadorVisibilidad.observe(estante.seccionEl);
  });

  document.getElementById('tabs-categorias').style.display = '';
}

async function mostrarModoResultados() {
  const primerCambio = modoActual !== 'resultados';
  modoActual = 'resultados';

  estantes.forEach((estante) => {
    observadorVisibilidad.unobserve(estante.seccionEl);
    estante.destruir();
    estante.seccionEl.style.display = 'none';
  });

  document.getElementById('tabs-categorias').style.display = 'none';

  if (!estanteResultados) {
    estanteResultados = new Estante({ id: 'resultados', nombre: 'Resultados' });
  } else {
    estanteResultados.seccionEl.style.display = '';
  }

  // Este estante SIEMPRE está visible en modo resultados, así que lo
  // inicializamos directo en vez de depender del IntersectionObserver
  // (si el usuario estaba scrolleado abajo, el observer podía no
  // dispararse nunca y el spinner quedaba pegado para siempre).
  if (!estanteResultados.renderer) {
    await estanteResultados.inicializar();
  }

  if (primerCambio) {
    estanteResultados.seccionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}
})();