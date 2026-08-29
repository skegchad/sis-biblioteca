import * as THREE from 'three';



(async function () {
  // window.APP_URL se define desde catalogo.php con tu constante $URL
  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;

    const contenedor = document.getElementById('estante-3d');
    const selCat = document.getElementById('f-categoria');
    const selTema = document.getElementById('f-tema');
    const inputBuscar = document.getElementById('f-buscar');
    const sinResultados = document.getElementById('sin-resultados');

    const botonInfo = document.getElementById('boton-info-libro');

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
    let librosEnEscena = [];
    let libroSeleccionado = null;
    let anchoFilaActual = 1;
    const SEPARACION = 0.02;

  // ============================================================
  // Escena base
  // ============================================================
  const escena = new THREE.Scene();
  escena.background = new THREE.Color(0xede7da);

  const ALTO_LIBRO = 1.55;
  const PROFUNDIDAD_LIBRO = 1.0; // tamaño de la portada, constante entre libros

  // Cámara ORTOGRÁFICA: a diferencia de una de perspectiva, el tamaño en
  // pantalla no depende de la distancia a la cámara, así los libros se ven
  // "flotando" a un tamaño consistente en vez de empequeñecerse por fuga
  // de perspectiva (que es lo que daba la sensación de "lejos").
  const camara = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 100);
  camara.position.set(0, ALTO_LIBRO / 2, 5);

  /* Ajusta el frustum de la cámara según el ancho real de la fila de libros
     y el aspecto del contenedor, para que siempre llenen el encuadre.
     Al estar la cámara centrada en Y sobre el centro vertical de los libros
     (arriba), top/bottom son simétricos: así no quedan pegados arriba. */
  function ajustarCamara(anchoFila) {
    const aspecto = contenedor.clientWidth / contenedor.clientHeight;
    const margen = 1.05; // aire extra alrededor de los libros

    let alturaFrustum = ALTO_LIBRO * margen;
    let anchoFrustum = alturaFrustum * aspecto;
    const anchoNecesario = anchoFila * margen;
    if (anchoFrustum < anchoNecesario) {
      anchoFrustum = anchoNecesario;
      alturaFrustum = anchoFrustum / aspecto;
    }

    camara.left = -anchoFrustum / 2;
    camara.right = anchoFrustum / 2;
    camara.top = alturaFrustum / 2;
    camara.bottom = -alturaFrustum / 2;
    camara.updateProjectionMatrix();
  }

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(contenedor.clientWidth, contenedor.clientHeight);
  contenedor.appendChild(renderer.domElement);

  // ============================================================
  // Reglas de color / tamaño (mismas que la versión CSS anterior)
  // ============================================================
  function colorTexto(hex) {
    const c = hex.replace('#', '');
    const r = parseInt(c.substr(0, 2), 16),
      g = parseInt(c.substr(2, 2), 16),
      b = parseInt(c.substr(4, 2), 16);
    const luminancia = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminancia > 0.55 ? '#20201c' : '#f7f4ec';
  }

  function sombrear(hex, porcentaje) {
    const c = hex.replace('#', '');
    let r = parseInt(c.substr(0, 2), 16),
      g = parseInt(c.substr(2, 2), 16),
      b = parseInt(c.substr(4, 2), 16);
    const f = porcentaje / 100;
    r = Math.round(r + (f < 0 ? r : 255 - r) * f);
    g = Math.round(g + (f < 0 ? g : 255 - g) * f);
    b = Math.round(b + (f < 0 ? b : 255 - b) * f);
    return `rgb(${r},${g},${b})`;
  }

  /* Grosor del libro (eje X) según número de páginas — mismo mapeo que la versión CSS,
     pero en unidades 3D en vez de píxeles */
  function grosorPorPaginas(paginas) {
    const MIN_PAG = 80,
      MAX_PAG = 900;
    const MIN_GROSOR = 0.1,
      MAX_GROSOR = 0.62;
    const p = Math.max(MIN_PAG, Math.min(MAX_PAG, paginas));
    const t = (p - MIN_PAG) / (MAX_PAG - MIN_PAG);
    return MIN_GROSOR + t * (MAX_GROSOR - MIN_GROSOR);
  }

  // ============================================================
  // Texturas generadas por canvas
  // ============================================================
  // Píxeles por unidad de mundo — mismo valor para ancho y alto, así el
  // canvas queda con la MISMA proporción que la cara real del libro y el
  // texto nunca se estira distinto según el grosor de cada libro.
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

    // Tamaño de letra definido en unidades de mundo (no en píxeles fijos):
    // así, subir ESCALA_LOMO para más nitidez no cambia el tamaño aparente
    // del texto, solo su resolución.
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

  // Píxeles por unidad de mundo para la portada (su cara es siempre
  // PROFUNDIDAD_LIBRO × ALTO_LIBRO, igual en todos los libros)
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
        envolverTexto(
          ctx,
          libro.titulo,
          margenPx,
          altoPx - fontAutorPx * 2.3,
          anchoPx - margenPx * 2,
          fontTituloPx * 1.2
        );

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
          // "cover": llena el canvas manteniendo la proporción de la imagen
          const escala = Math.max(anchoPx / img.width, altoPx / img.height);
          const w = img.width * escala,
            h = img.height * escala;
          ctx.drawImage(img, (anchoPx - w) / 2, (altoPx - h) / 2, w, h);
          dibujarEtiquetaYResolver();
        };
        img.onerror = () => {
          fondoDegradado();
          dibujarEtiquetaYResolver();
        };
        img.src = libro.rutaFotoAbsoluta;
      } else {
        fondoDegradado();
        dibujarEtiquetaYResolver();
      }
    });
  }

  // Canto de páginas: una sola textura reutilizada por todos los libros (más liviano)
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

  // ============================================================
  // Construcción de cada libro (BoxGeometry real, no un truco 2D)
  // ============================================================
  async function crearLibro3D(libro) {
    const grosor = grosorPorPaginas(libro.paginas);
    const geometria = new THREE.BoxGeometry(grosor, ALTO_LIBRO, PROFUNDIDAD_LIBRO);

    const [texLomo, texPortada] = await Promise.all([
      Promise.resolve(texturaLomo(libro, grosor)),
      texturaPortada(libro),
    ]);

    const materialLomo = new THREE.MeshBasicMaterial({ map: texLomo });
    const materialPortada = new THREE.MeshBasicMaterial({ map: texPortada });

    // Orden de caras que espera BoxGeometry: +x, -x, +y, -y, +z, -z.
    // La portada va en +x (no en -z): al girar la malla -90° sobre su eje Y,
    // es la cara +x la que queda mirando a la cámara — por eso el giro es
    // de 90°, no de 180° (180° mostraría la cara opuesta al lomo, que no
    // es donde va la portada).
    const materiales = [
      materialPortada, // +x portada (se revela al girar -90°)
      materialPaginas, // -x canto izquierdo
      materialPaginas, // +y canto superior
      materialPaginas, // -y base
      materialLomo, //   +z lomo (mira hacia la cámara en reposo)
      materialPaginas, // -z canto trasero
    ];

    const malla = new THREE.Mesh(geometria, materiales);
    malla.position.y = ALTO_LIBRO / 2;
    malla.userData = { libro, grosor, abierto: false, posXBase: 0, posZBase: 0 };
    return malla;
  }

  function actualizarBotonInfo() {
    if (!libroSeleccionado) {
        botonInfo.style.display = 'none';
        return;
    }

    const malla = libroSeleccionado;

    const caja = new THREE.Box3().setFromObject(malla);

    const esquina = new THREE.Vector3(
        caja.min.x,
        caja.max.y,
        caja.max.z
    );

    esquina.project(camara);

    const rect = renderer.domElement.getBoundingClientRect();

    const x = ((esquina.x + 1) / 2) * rect.width;
    const y = ((-esquina.y + 1) / 2) * rect.height;

    botonInfo.style.display = 'block';

    botonInfo.style.left = `${x - 8}px`;
    botonInfo.style.top = `${y - 8}px`;
    }

  // ============================================================
  // Layout de la fila y estado de selección
  // ============================================================
  

  function acomodarEnFila() {
    const anchoTotal = librosEnEscena.reduce(
        (acc, m) => acc + m.userData.grosor + SEPARACION,
        0
    );

    anchoFilaActual = anchoTotal || 1;

    let x = -anchoTotal / 2;

    librosEnEscena.forEach((malla) => {
        const g = malla.userData.grosor;

        const posX = x + g / 2;

        malla.userData.posXBase = posX;
        malla.userData.posZBase = 0;

        malla.position.x = posX;
        malla.position.z = 0;

        x += g + SEPARACION;
    });

    ajustarCamara(anchoFilaActual);
    }

  async function pintarEstante(libros) {
    librosEnEscena.forEach((m) => escena.remove(m));
    librosEnEscena = [];
    libroSeleccionado = null;
    sinResultados.style.display = libros.length ? 'none' : 'block';

    for (const libro of libros) {
      const rutaFotoAbsoluta = libro.ruta_foto
        ? `${BASE}/${libro.ruta_foto}`.replace(/([^:]\/)\/+/g, '$1')
        : null;
      const malla = await crearLibro3D({ ...libro, rutaFotoAbsoluta });
      escena.add(malla);
      librosEnEscena.push(malla);
    }
    acomodarEnFila();
  }

  // ============================================================
  // Interacción: click con raycasting para centrar/girar un libro
  // ============================================================
  const raycaster = new THREE.Raycaster();
  const puntero = new THREE.Vector2();

  function alClick(ev) {
    const rect = renderer.domElement.getBoundingClientRect();
    puntero.x = ((ev.clientX - rect.left) / rect.width) * 2 - 1;
    puntero.y = -((ev.clientY - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(puntero, camara);
    const intersecciones = raycaster.intersectObjects(librosEnEscena);

    if (!intersecciones.length) {
      cerrarSeleccionado();
      return;
    }
    const malla = intersecciones[0].object;
    malla === libroSeleccionado ? cerrarSeleccionado() : seleccionarLibro(malla);
  }
  renderer.domElement.addEventListener('click', alClick);

  const Z_SELECCIONADO = 2;

    function seleccionarLibro(malla) {
    cerrarSeleccionado();

    libroSeleccionado = malla;
    malla.userData.abierto = true;

    const indice = librosEnEscena.indexOf(malla);
    const MARGEN = 1.25;

    // ============================================================
    // LIBRO SELECCIONADO
    // ============================================================

    animarValor(malla.position, 'x', 0, 600);
    animarValor(malla.position, 'z', 1.0, 600);
    animarValor(malla.rotation, 'y', -Math.PI / 2, 600);

    // ============================================================
    // LIBRO INMEDIATAMENTE A LA IZQUIERDA
    // ============================================================

    if (indice > 0) {
        const izquierda = librosEnEscena[indice - 1];

        const nuevoX =
        -(malla.userData.grosor / 2) -
        MARGEN -
        (izquierda.userData.grosor / 2);

        const desplazamiento =
        nuevoX - izquierda.userData.posXBase;

        for (let i = 0; i < indice; i++) {
        const libro = librosEnEscena[i];

        animarValor(
            libro.position,
            'x',
            libro.userData.posXBase + desplazamiento,
            600
        );

        animarOpacidad(libro, 0.35, 400);
        }
    }

    // ============================================================
    // LIBRO INMEDIATAMENTE A LA DERECHA
    // ============================================================

    if (indice < librosEnEscena.length - 1) {
        const derecha = librosEnEscena[indice + 1];

        const nuevoX =
        (malla.userData.grosor / 2) +
        MARGEN +
        (derecha.userData.grosor / 2);

        const desplazamiento =
        nuevoX - derecha.userData.posXBase;

        for (let i = indice + 1; i < librosEnEscena.length; i++) {
        const libro = librosEnEscena[i];

        animarValor(
            libro.position,
            'x',
            libro.userData.posXBase + desplazamiento,
            600
        );

        animarOpacidad(libro, 0.35, 400);
        }
    }
    }

  function cerrarSeleccionado() {
    if (!libroSeleccionado) return;

    infoLibro.classList.remove('visible');

    const mallaSeleccionada = libroSeleccionado;

    mallaSeleccionada.userData.abierto = false;

    // Volver el libro seleccionado a su posición original
    animarValor(
        mallaSeleccionada.position,
        'x',
        mallaSeleccionada.userData.posXBase,
        600
    );

    animarValor(
        mallaSeleccionada.position,
        'z',
        mallaSeleccionada.userData.posZBase,
        600
    );

    animarValor(
        mallaSeleccionada.rotation,
        'y',
        0,
        600
    );

    // Volver todos los demás a su posición original
    librosEnEscena.forEach((m) => {
        if (m !== mallaSeleccionada) {
        animarValor(
            m.position,
            'x',
            m.userData.posXBase,
            600
        );

        animarOpacidad(m, 1, 400);
        }
    });

    libroSeleccionado = null;
    }

  // Tween genérico (ease-out cúbico) sin depender de librerías externas
  function animarValor(objeto, propiedad, destino, duracionMs) {
    const inicio = objeto[propiedad];
    const t0 = performance.now();
    requestAnimationFrame(function tick(ahora) {
      const t = Math.min(1, (ahora - t0) / duracionMs);
      const suavizado = 1 - Math.pow(1 - t, 3);
      objeto[propiedad] = inicio + (destino - inicio) * suavizado;
      if (t < 1) requestAnimationFrame(tick);
    });
  }

  function animarOpacidad(malla, destino, duracionMs) {
    malla.material.forEach((mat) => (mat.transparent = true));
    const inicio = malla.material[0].opacity ?? 1;
    const t0 = performance.now();
    requestAnimationFrame(function tick(ahora) {
      const t = Math.min(1, (ahora - t0) / duracionMs);
      const valor = inicio + (destino - inicio) * t;
      malla.material.forEach((mat) => (mat.opacity = valor));
      if (t < 1) requestAnimationFrame(tick);
    });
  }

  // ============================================================
  // Render loop y resize
  // ============================================================
  (function animar() {
    requestAnimationFrame(animar);

    actualizarBotonInfo();

    renderer.render(escena, camara);
    })();

  window.addEventListener('resize', () => {
    renderer.setSize(contenedor.clientWidth, contenedor.clientHeight);
    ajustarCamara(anchoFilaActual);
  });

  // ============================================================
  // Filtros y búsqueda (igual comportamiento que la versión anterior)
  // ============================================================
  async function cargarFiltros() {
    try {
      const res = await fetch(API_FILTROS);
      const data = await res.json();
      selCat.innerHTML =
        '<option value="">Todas las categorías</option>' +
        data.categorias.map((c) => `<option value="${c}">${c}</option>`).join('');
      selTema.innerHTML =
        '<option value="">Todos los temas</option>' +
        data.temas.map((t) => `<option value="${t}">${t}</option>`).join('');
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
      const res = await fetch(`${API_BUSCAR}?${params.toString()}`);
      const libros = await res.json();
      await pintarEstante(libros);
    } catch (err) {
      console.error('Error buscando libros', err);
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
  function mostrarInformacionLibro(libro) {

  if (!libro) return;

  // Información básica
  infoTitulo.textContent = libro.titulo || 'Sin título';
  infoAutor.textContent = libro.autor || 'Autor desconocido';
  infoDescripcion.textContent = libro.descripcion || 'Sin descripción';

  // Portada
  if (libro.ruta_foto) {
    const rutaPortada = `${BASE}/${libro.ruta_foto}`
      .replace(/([^:]\/)\/+/g, '$1');

    infoPortada.src = rutaPortada;
    infoPortada.alt = `Portada de ${libro.titulo || 'libro'}`;
  } else {
    infoPortada.src = '';
    infoPortada.alt = 'Portada del libro';
  }

  // Metadatos
  infoPaginas.textContent = libro.paginas || '—';
  infoCategoria.textContent = libro.categoria || '—';
  infoTema.textContent = libro.temas || '—';
  infoTipo.textContent = libro.tipo || '—';
  infoIdioma.textContent = libro.idioma || '—';
  infoEdicion.textContent = libro.edicion || '—';

  // Ejemplares y préstamos
  const ejemplares = Number(libro.ejemplares) || 0;
  const prestados = Number(libro.prestados) || 0;

  infoEjemplares.textContent = ejemplares;
  infoPrestados.textContent = prestados;

  // Disponibilidad
  if (ejemplares > prestados) {
    infoDisponibilidad.textContent = 'Disponible';
    infoDisponibilidad.classList.remove('prestado');
    infoDisponibilidad.classList.add('disponible');
  } else {
    infoDisponibilidad.textContent = 'No disponible';
    infoDisponibilidad.classList.remove('disponible');
    infoDisponibilidad.classList.add('prestado');
  }

  // PDF
  if (libro.ruta_pdf) {
    const rutaPDF = `${BASE}/${libro.ruta_pdf}`
      .replace(/([^:]\/)\/+/g, '$1');

    botonLeerLibro.href = rutaPDF;
    botonLeerLibro.style.display = 'flex';
  } else {
    botonLeerLibro.removeAttribute('href');
    botonLeerLibro.style.display = 'none';
  }

  // Mostrar tarjeta
  infoLibro.classList.add('visible');
}
botonInfo.addEventListener('click', (ev) => {
  ev.stopPropagation();

  if (!libroSeleccionado) return;

  mostrarInformacionLibro(libroSeleccionado.userData.libro);
});
})();

