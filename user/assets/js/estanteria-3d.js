import * as THREE from 'three';



(async function () {
  // window.APP_URL se define desde catalogo.php con tu constante $URL
  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;
  const cargando = document.getElementById('cargando-estantes');

  const contenedorCategorias =
      document.getElementById('estantes-categorias');

  const contenedor =
    document.getElementById('estantes-categorias');

  const vistaBusqueda =
      document.getElementById('vista-busqueda');

  const contenedorResultados =
      document.getElementById('estante-resultados');
  const selCat =
  document.getElementById('f-categoria');

  const selSubcat =
    document.getElementById('f-subcategoria');

  const selTipo =
    document.getElementById('f-tipo');

  const selIdioma =
    document.getElementById('f-idioma');

  const selDisponibilidad =
    document.getElementById('f-disponibilidad');

  const inputBuscar =
    document.getElementById('f-buscar');

  const botonFiltros =
    document.getElementById('boton-filtros');

  const panelFiltros =
    document.getElementById('panel-filtros');

  const cerrarFiltros =
    document.getElementById('cerrar-filtros');

  const abrirTemas =
    document.getElementById('abrir-temas');

  const panelTemas =
    document.getElementById('panel-temas');

  const cerrarTemas =
    document.getElementById('cerrar-temas');

  const aceptarTemas =
    document.getElementById('aceptar-temas');

  const buscarTema =
    document.getElementById('buscar-tema');

  const listaTemas =
    document.getElementById('lista-temas');

  const temasSeleccionadosVisual =
    document.getElementById('temas-seleccionados');


  const temasSeleccionadosDiv =
    document.getElementById('temas-seleccionados');

  const btnLimpiarFiltros =
    document.getElementById('limpiar-filtros');

  const sinResultados =
    document.getElementById('sin-resultados');


  let datosFiltros = {

    categorias: [],
    subcategorias: [],
    tipos: [],
    idiomas: [],
    temas: []

  };


  let temasSeleccionados = [];

  let temasTemporales = [];
    const botonInfo = document.getElementById('boton-info-libro');
    const controlesLibro = document.getElementById('controles-libro');
    const botonAnterior = document.getElementById('libro-anterior');
    const botonSiguiente = document.getElementById('libro-siguiente');
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
    let estantes = [];

    let estanteActivo = null;

    let librosEnEscena = [];

    let libroSeleccionado = null;
    let anchoFilaActual = 1;
    let desplazamientoX = 0;
    let desplazamientoVisualX = 0;

    const SUAVIDAD_DESPLAZAMIENTO = 0.12;
    let arrastrando = false;
    let inicioArrastreX = 0;
    let desplazamientoInicioX = 0;
    let huboArrastre = false;
    let velocidadDesplazamiento = 0;
    let ultimoX = 0;
    let ultimoTiempo = 0;
    let animandoDesplazamiento = false;
    const animacionesLibros = new WeakMap();
    const SEPARACION = 0.02;
    function detenerAnimacionLibro(malla) {
        const animacion = animacionesLibros.get(malla);

        if (animacion) {
            animacion.cancelado = true;
            animacionesLibros.delete(malla);
        }
    }
    

  // ============================================================
  // Escena base
  // ============================================================
  const escena = new THREE.Scene();
  
  escena.background = new THREE.Color(0xede7da);
  const ALTO_LIBRO = 1.55;
  const PROFUNDIDAD_LIBRO = 1.0; // tamaño de la portada, constante entre libros
  function crearEstante(categoria) {

    const grupo = new THREE.Group();

    grupo.userData = {
        categoriaId: categoria.id,
        categoriaNombre: categoria.nombre
    };

    escena.add(grupo);

    const estante = {
        grupo: grupo,
        libros: [],
        libroSeleccionado: null,
        desplazamientoX: 0,
        desplazamientoVisualX: 0,
        anchoFila: 1
    };

    estantes.push(estante);

    return estante;
}
function crearEstantesCategorias(categorias) {

    // Limpiar estantes anteriores
    estantes.forEach(estante => {
        escena.remove(estante.grupo);
    });

    estantes.length = 0;

    const SEPARACION_ESTANTES = 2.2;

    categorias.forEach((categoria, indice) => {

        const estante = crearEstante(categoria);

        // Cada categoría queda debajo de la anterior
        estante.grupo.position.set(
            0,
            -indice * SEPARACION_ESTANTES,
            0
        );

        console.log(
            'Estante creado:',
            categoria.nombre,
            estante
        );

    });
}
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

    const aspecto =
        contenedor.clientWidth / contenedor.clientHeight;

    const esMovil =
        window.matchMedia('(max-width: 768px)').matches;

    // PC: libro grande.
    // Móvil: pequeño zoom out.
    const margen = esMovil ? 1.15 : 1.02;

    const alturaFrustum =
        ALTO_LIBRO * margen;

    const anchoFrustum =
        alturaFrustum * aspecto;

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
  function acomodarLibrosEstante(estante) {

    const anchoTotal = estante.libros.reduce(
        (acc, malla) =>
            acc +
            malla.userData.grosor +
            SEPARACION,
        0
    );

    estante.anchoFila =
        anchoTotal || 1;


    let x =
        -anchoTotal / 2;


    estante.libros.forEach(malla => {

        const grosor =
            malla.userData.grosor;


        const posX =
            x + grosor / 2;


        malla.userData.posXBase =
            posX;

        malla.userData.posZBase =
            0;


        malla.position.x =
            posX;

        malla.position.z =
            0;


        x +=
            grosor +
            SEPARACION;

    });

}
  
  function obtenerLimiteDesplazamiento() {

    const anchoVisible =
        camara.right - camara.left;

    const exceso =
        anchoFilaActual - anchoVisible;

    return Math.max(0, exceso / 2);
}


function aplicarDesplazamiento() {

    // Si hay un libro seleccionado, NO aplicamos
    // el desplazamiento horizontal general.
    // Los libros están siendo controlados por las animaciones
    // de seleccionarLibro().
    if (libroSeleccionado) return;

    librosEnEscena.forEach((malla) => {

        malla.position.x =
            malla.userData.posXBase + desplazamientoVisualX;
    });
}

  async function pintarEstante(estante, libros) {

    // Eliminar libros anteriores de este estante
    estante.libros.forEach(malla => {
        estante.grupo.remove(malla);

        // Liberar recursos de Three.js
        if (malla.geometry) {
            malla.geometry.dispose();
        }

        if (malla.material) {
            malla.material.forEach(mat => {

                if (mat.map) {
                    mat.map.dispose();
                }

                mat.dispose();

            });
        }
    });

    estante.libros = [];

    estante.libroSeleccionado = null;
    estante.desplazamientoX = 0;
    estante.desplazamientoVisualX = 0;


    for (const libro of libros) {

        const rutaFotoAbsoluta = libro.ruta_foto
            ? `${BASE}/${libro.ruta_foto}`
                .replace(/([^:]\/)\/+/g, '$1')
            : null;


        const malla = await crearLibro3D({
            ...libro,
            rutaFotoAbsoluta
        });


        // IMPORTANTE:
        // El libro pertenece al grupo de su categoría
        estante.grupo.add(malla);

        estante.libros.push(malla);
    }


    acomodarLibrosEstante(estante);
        if (estante === estanteActivo) {
            librosEnEscena = estante.libros;
            anchoFilaActual = estante.anchoFila;
        }
}
function activarEstante(estante) {

    estanteActivo = estante;

    librosEnEscena = estante.libros;

    anchoFilaActual = estante.anchoFila;

    desplazamientoX =
        estante.desplazamientoX;

    desplazamientoVisualX =
        estante.desplazamientoVisualX;

    libroSeleccionado = null;

    actualizarControlesLibro();
}
function obtenerEstanteDeLibro(malla) {

    for (const estante of estantes) {

        if (estante.libros.includes(malla)) {
            return estante;
        }

    }

    return null;
}

  // ============================================================
  // Interacción: click con raycasting para centrar/girar un libro
  // ============================================================
  const raycaster = new THREE.Raycaster();
  const puntero = new THREE.Vector2();

  renderer.domElement.addEventListener('mousemove', (ev) => {

    // Mientras se está arrastrando, el cursor de arrastre
    // tiene prioridad absoluta.
    if (arrastrando) {
        renderer.domElement.style.cursor = 'grabbing';
        return;
    }

    const rect =
        renderer.domElement.getBoundingClientRect();

    puntero.x =
        ((ev.clientX - rect.left) / rect.width) * 2 - 1;

    puntero.y =
        -((ev.clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(puntero, camara);

    const todosLosLibros =
    estantes.flatMap(estante => estante.libros);

    const intersecciones =
        raycaster.intersectObjects(todosLosLibros);

    if (intersecciones.length > 0) {

        renderer.domElement.style.cursor =
            'pointer';

    } else {

        renderer.domElement.style.cursor =
            'default';
    }
});
  renderer.domElement.addEventListener('pointerdown', (ev) => {

    if (libroSeleccionado) return;

    arrastrando = true;
    huboArrastre = false;

    inicioArrastreX = ev.clientX;
    desplazamientoInicioX = desplazamientoX;

    ultimoX = ev.clientX;
    ultimoTiempo = performance.now();

    velocidadDesplazamiento = 0;

    renderer.domElement.setPointerCapture(ev.pointerId);

    renderer.domElement.style.cursor = 'grabbing';
});


renderer.domElement.addEventListener('pointermove', (ev) => {

    if (!arrastrando) return;

    const diferencia = ev.clientX - inicioArrastreX;

    if (Math.abs(diferencia) > 5) {
        huboArrastre = true;
    }

    const limite = obtenerLimiteDesplazamiento();

    desplazamientoX =
        desplazamientoInicioX + diferencia;

    desplazamientoX = Math.max(
        -limite,
        Math.min(limite, desplazamientoX)
    );

    aplicarDesplazamiento();
});


  renderer.domElement.addEventListener('pointerup', (ev) => {

    if (!arrastrando) return;

    arrastrando = false;

    renderer.domElement.releasePointerCapture(ev.pointerId);

    renderer.domElement.style.cursor = 'default';

    // La bandera solo debe durar hasta el siguiente click
});
  function iniciarInercia() {

    if (Math.abs(velocidadDesplazamiento) < 0.01) {
        return;
    }

    if (animandoDesplazamiento) return;

    animandoDesplazamiento = true;

    const limite =
        obtenerLimiteDesplazamiento();

    function animar() {

        if (arrastrando) {
            animandoDesplazamiento = false;
            return;
        }

        desplazamientoX +=
            velocidadDesplazamiento * 16;

        // Frenado progresivo
        velocidadDesplazamiento *= 0.92;

        // Límites
        if (desplazamientoX > limite) {

            desplazamientoX = limite;

            velocidadDesplazamiento *= 0.5;
        }

        if (desplazamientoX < -limite) {

            desplazamientoX = -limite;

            velocidadDesplazamiento *= 0.5;
        }

        aplicarDesplazamiento();

        if (Math.abs(velocidadDesplazamiento) > 0.01) {

            requestAnimationFrame(animar);

        } else {

            animandoDesplazamiento = false;

        }
    }

    requestAnimationFrame(animar);
}
  function alClick(ev) {

    if (huboArrastre) {
        huboArrastre = false;
        return;
    }

    const rect =
        renderer.domElement.getBoundingClientRect();

    puntero.x =
        ((ev.clientX - rect.left) / rect.width) * 2 - 1;

    puntero.y =
        -((ev.clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(puntero, camara);

    const todosLosLibros =
    estantes.flatMap(estante => estante.libros);

const intersecciones =
    raycaster.intersectObjects(todosLosLibros);

    if (!intersecciones.length) {
        cerrarSeleccionado();
        return;
    }

    const malla =
        intersecciones[0].object;

    const estante =
    obtenerEstanteDeLibro(malla);

    if (estante && estante !== estanteActivo) {
        activarEstante(estante);
    }

    malla === libroSeleccionado
        ? cerrarSeleccionado()
        : seleccionarLibro(malla);
}
  renderer.domElement.addEventListener('click', alClick);

  const Z_SELECCIONADO = 2;

  function seleccionarLibro(malla) {

    if (malla === libroSeleccionado) return;

    // Si había otro libro abierto, cerrarlo visualmente
    // antes de establecer el nuevo estado.
    if (libroSeleccionado) {

        librosEnEscena.forEach(libro => {

            animarLibro(libro, {
                x: libro.userData.posXBase,
                z: libro.userData.posZBase,
                rotY: 0,
                opacidad: 1
            }, 500);

        });
    }

    desplazamientoX = 0;
    desplazamientoVisualX = 0;

    libroSeleccionado = malla;

    botonInfo.textContent = '˅';
    botonInfo.setAttribute(
        'aria-label',
        'Ver información'
    );

    malla.userData.abierto = true;

    const indice =
        librosEnEscena.indexOf(malla);

    const MARGEN = 1.25;

    // ============================================================
    // LIBRO SELECCIONADO
    // ============================================================

    animarLibro(malla, {
        x: 0,
        z: Z_SELECCIONADO,
        rotY: -Math.PI / 2,
        opacidad: 1
    }, 600);


    // ============================================================
    // LIBROS DE LA IZQUIERDA
    // ============================================================

    if (indice > 0) {

        const izquierda =
            librosEnEscena[indice - 1];

        const nuevoX =
            -(malla.userData.grosor / 2) -
            MARGEN -
            (izquierda.userData.grosor / 2);

        const desplazamiento =
            nuevoX -
            izquierda.userData.posXBase;

        for (let i = 0; i < indice; i++) {

            const libro =
                librosEnEscena[i];

            animarLibro(libro, {
                x:
                    libro.userData.posXBase +
                    desplazamiento,

                z:
                    libro.userData.posZBase,

                rotY: 0,

                opacidad: 0.35

            }, 600);
        }
    }


    // ============================================================
    // LIBROS DE LA DERECHA
    // ============================================================

    if (
        indice <
        librosEnEscena.length - 1
    ) {

        const derecha =
            librosEnEscena[indice + 1];

        const nuevoX =
            (malla.userData.grosor / 2) +
            MARGEN +
            (derecha.userData.grosor / 2);

        const desplazamiento =
            nuevoX -
            derecha.userData.posXBase;

        for (
            let i = indice + 1;
            i < librosEnEscena.length;
            i++
        ) {

            const libro =
                librosEnEscena[i];

            animarLibro(libro, {
                x:
                    libro.userData.posXBase +
                    desplazamiento,

                z:
                    libro.userData.posZBase,

                rotY: 0,

                opacidad: 0.35

            }, 600);
        }
    }

    actualizarControlesLibro();
}

  function cerrarSeleccionado() {

    if (!libroSeleccionado) return;

    infoLibro.classList.remove('visible');

    const mallaSeleccionada =
        libroSeleccionado;

    mallaSeleccionada.userData.abierto = false;


    // ============================================================
    // TODOS LOS LIBROS REGRESAN JUNTOS
    // ============================================================

    librosEnEscena.forEach(malla => {

        animarLibro(malla, {

            x:
                malla.userData.posXBase,

            z:
                malla.userData.posZBase,

            rotY: 0,

            opacidad: 1

        }, 600);

    });


    botonInfo.textContent = '˅';

    botonInfo.setAttribute(
        'aria-label',
        'Ver información'
    );

    desplazamientoX = 0;
    desplazamientoVisualX = 0;


    // MUY IMPORTANTE:
    // No borrar libroSeleccionado inmediatamente.
    // Esperamos a que termine la animación.
    setTimeout(() => {

        // Solo limpiar si sigue siendo el mismo libro.
        if (libroSeleccionado === mallaSeleccionada) {

            libroSeleccionado = null;

            actualizarControlesLibro();
        }

    }, 620);
}

  // Tween genérico (ease-out cúbico) sin depender de librerías externas
  function animarLibro(malla, destino, duracion = 600, alFinal = null) {

    detenerAnimacionLibro(malla);

    const inicio = {
        x: malla.position.x,
        z: malla.position.z,
        rotY: malla.rotation.y,
        opacidad: malla.material[0].opacity ?? 1
    };

    const animacion = {
        cancelado: false
    };

    animacionesLibros.set(malla, animacion);

    const t0 = performance.now();

    function tick(ahora) {

        if (animacion.cancelado) return;

        const t = Math.min(
            1,
            (ahora - t0) / duracion
        );

        // Ease-out cúbico
        const suavizado =
            1 - Math.pow(1 - t, 3);

        malla.position.x =
            inicio.x +
            (destino.x - inicio.x) *
            suavizado;

        malla.position.z =
            inicio.z +
            (destino.z - inicio.z) *
            suavizado;

        malla.rotation.y =
            inicio.rotY +
            (destino.rotY - inicio.rotY) *
            suavizado;

        const opacidad =
            inicio.opacidad +
            (destino.opacidad - inicio.opacidad) *
            suavizado;

        malla.material.forEach(mat => {
            mat.transparent = true;
            mat.opacity = opacidad;
        });

        if (t < 1) {

            requestAnimationFrame(tick);

        } else {

            animacionesLibros.delete(malla);

            if (alFinal) {
                alFinal();
            }
        }
    }

    requestAnimationFrame(tick);
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
  function animarDesplazamientoSuave() {

    if (!libroSeleccionado) {

        desplazamientoVisualX +=
            (desplazamientoX - desplazamientoVisualX) *
            SUAVIDAD_DESPLAZAMIENTO;

        aplicarDesplazamiento();
    }

    requestAnimationFrame(animarDesplazamientoSuave);
}

  animarDesplazamientoSuave();

  (function animar() {
    requestAnimationFrame(animar);

    actualizarBotonInfo();

    renderer.render(escena, camara);
    })();

  window.addEventListener('resize', () => {
    renderer.setSize(contenedor.clientWidth, contenedor.clientHeight);
    ajustarCamara();
  });

  // ============================================================
  // Filtros y búsqueda (igual comportamiento que la versión anterior)
  // ============================================================
  async function cargarFiltros() {

  try {

    const res =
      await fetch(API_FILTROS);

    const data =
      await res.json();

    datosFiltros = data;

    crearEstantesCategorias(data.categorias);
    // Categorías

    selCat.innerHTML =
      '<option value="">Todas las categorías</option>' +

      data.categorias.map(c => `
        <option value="${c.nombre}">
          ${c.nombre}
        </option>
      `).join('');


    // Tipos

    selTipo.innerHTML =
      '<option value="">Todos los tipos</option>' +

      data.tipos.map(t => `
        <option value="${t.nombre}">
          ${t.nombre}
        </option>
      `).join('');


    // Idiomas

    selIdioma.innerHTML =
      '<option value="">Todos los idiomas</option>' +

      data.idiomas.map(i => `
        <option value="${i}">
          ${i}
        </option>
      `).join('');


    actualizarSubcategorias();

    dibujarTemas();

  } catch (err) {

    console.error(
      'No se pudieron cargar los filtros',
      err
    );

  }
}
  function actualizarSubcategorias() {

  const categoriaNombre = selCat.value;


  // Si no hay categoría seleccionada
  if (!categoriaNombre) {

    selSubcat.innerHTML =
      '<option value="">Todas las subcategorías</option>';

    selSubcat.disabled = true;

    return;
  }


  // Buscar la categoría seleccionada
  const categoria = datosFiltros.categorias.find(
    c => c.nombre === categoriaNombre
  );


  if (!categoria) {

    selSubcat.innerHTML =
      '<option value="">Todas las subcategorías</option>';

    selSubcat.disabled = true;

    return;
  }


  // Subcategorías pertenecientes a esa categoría
  const subcategorias =
    datosFiltros.subcategorias.filter(
      s => Number(s.categoria_id) === Number(categoria.id)
    );


  selSubcat.disabled = subcategorias.length === 0;


  selSubcat.innerHTML =
    '<option value="">Todas las subcategorías</option>' +

    subcategorias.map(s => `
      <option value="${s.nombre}">
        ${s.nombre}
      </option>
    `).join('');
}
function dibujarTemas() {

  const texto = buscarTema.value
    .trim()
    .toLowerCase();

  const temasFiltrados = datosFiltros.temas.filter(tema =>
    tema.nombre.toLowerCase().includes(texto)
  );

  listaTemas.innerHTML = '';

  temasFiltrados.forEach(tema => {

    const boton = document.createElement('button');

    boton.type = 'button';
    boton.className = 'tema-chip';

    // MOSTRAR EL NOMBRE
    boton.textContent = tema.nombre;

    // PERO GUARDAR/COMPARAR EL ID
    if (temasTemporales.includes(Number(tema.id))) {
      boton.classList.add('seleccionado');
    }

    boton.addEventListener('click', () => {

      const id = Number(tema.id);

      if (temasTemporales.includes(id)) {

        temasTemporales = temasTemporales.filter(
          t => t !== id
        );

      } else {

        temasTemporales.push(id);

      }

      dibujarTemas();

    });

    listaTemas.appendChild(boton);

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
        buscar: inputBuscar.value.trim() || ''
    });

    temasSeleccionados.forEach(id => {
        params.append('temas[]', id);
    });

    try {

        const res = await fetch(
            `${API_BUSCAR}?${params.toString()}`
        );

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const libros = await res.json();

        console.log('Libros recibidos:', libros);

        // ========================================================
        // LIMPIAR ESTADO ANTERIOR
        // ========================================================

        libroSeleccionado = null;
        librosEnEscena = [];


        // ========================================================
        // DISTRIBUIR LIBROS EN SUS CATEGORÍAS
        // ========================================================

        for (const estante of estantes) {

            const categoriaNombre =
                estante.grupo.userData.categoriaNombre;

            const librosCategoria =
                libros.filter(libro =>
                    libro.categoria === categoriaNombre
                );

            await pintarEstante(
                estante,
                librosCategoria
            );
        }
        if (estantes.length > 0) {

            activarEstante(estantes[0]);

        }

        // ========================================================
        // LISTA GLOBAL PARA LA INTERACCIÓN
        // ========================================================


        // ========================================================
        // SIN RESULTADOS
        // ========================================================

        if (libros.length === 0) {

            sinResultados.style.display =
                'block';

        } else {

            sinResultados.style.display =
                'none';
        }


        ocultarCarga();

    } catch (err) {

        console.error(
            'Error buscando libros',
            err
        );

        sinResultados.textContent =
            'Ocurrió un error cargando los libros.';

        sinResultados.style.display =
            'block';

        ocultarCarga();
    }
}

function limpiarTemas() {
    temasSeleccionados = [];

    document.querySelectorAll('.tema-chip').forEach(chip => {
        chip.classList.remove('activo');
    });

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

btnLimpiarFiltros.addEventListener('click', limpiarFiltros);

  let temporizador;
  function buscarConDebounce() {
    clearTimeout(temporizador);
    temporizador = setTimeout(buscarLibros, 300);
  }

  selCat.addEventListener('change', () => {

    actualizarSubcategorias();

    buscarLibros();

  });


  selSubcat.addEventListener(
    'change',
    buscarLibros
  );


  selTipo.addEventListener(
    'change',
    buscarLibros
  );


  selIdioma.addEventListener(
    'change',
    buscarLibros
  );


  selDisponibilidad.addEventListener(
    'change',
    buscarLibros
  );


  inputBuscar.addEventListener(
    'input',
    buscarConDebounce
  );


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

  // Si la tarjeta está abierta, cerrarla
  if (infoLibro.classList.contains('visible')) {

    infoLibro.classList.remove('visible');

    botonInfo.textContent = '˅';
    botonInfo.setAttribute('aria-label', 'Ver información');

  } 
  // Si está cerrada, abrirla
  else {

    mostrarInformacionLibro(libroSeleccionado.userData.libro);

    botonInfo.textContent = '⌃';
    botonInfo.setAttribute('aria-label', 'Cerrar información');

  }

});
buscarTema.addEventListener(
  'input',
  dibujarTemas
);
botonFiltros.addEventListener(
  'click',
  () => {

    panelFiltros.classList.add(
      'abierto'
    );

  }
);


cerrarFiltros.addEventListener(
  'click',
  () => {

    panelFiltros.classList.remove(
      'abierto'
    );

  }
);
abrirTemas.addEventListener(
  'click',
  () => {

    temasTemporales = [
      ...temasSeleccionados
    ];

    buscarTema.value = '';

    dibujarTemas();

    panelTemas.classList.add(
      'abierto'
    );

  }
);
aceptarTemas.addEventListener(
  'click',
  () => {

    temasSeleccionados = [
      ...temasTemporales
    ];

    actualizarTextoTemas();

    panelTemas.classList.remove('abierto');

    buscarLibros();

  }
);
cerrarTemas.addEventListener(
  'click',
  () => {

    panelTemas.classList.remove(
      'abierto'
    );

  }
);
function actualizarTextoTemas() {

    const contenedor =
        document.getElementById('temas-seleccionados');

    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (temasSeleccionados.length === 0) {

        contenedor.textContent =
            'Ningún tema seleccionado';

        return;
    }

    temasSeleccionados.forEach(id => {

        const tema =
            datosFiltros.temas.find(
                t => Number(t.id) === Number(id)
            );

        if (!tema) return;

        const chip =
            document.createElement('span');

        chip.className =
            'tema-chip seleccionado';

        chip.textContent =
            tema.nombre;

        contenedor.appendChild(chip);
    });
}
function mostrarCarga() {
    cargando.classList.remove('oculto');
}

function ocultarCarga() {
    cargando.classList.add('oculto');
}
function actualizarControlesLibro() {

    if (!libroSeleccionado) {
        controlesLibro.style.display = 'none';
        return;
    }

    controlesLibro.style.display = 'flex';

    const indice = librosEnEscena.indexOf(libroSeleccionado);

    botonAnterior.disabled = indice <= 0;
    botonSiguiente.disabled = indice >= librosEnEscena.length - 1;
}
function seleccionarLibroAdyacente(direccion) {

    if (!libroSeleccionado) return;

    const indice = librosEnEscena.indexOf(libroSeleccionado);

    if (indice === -1) return;

    const nuevoIndice = indice + direccion;

    if (
        nuevoIndice < 0 ||
        nuevoIndice >= librosEnEscena.length
    ) {
        return;
    }

    seleccionarLibro(librosEnEscena[nuevoIndice]);
}
botonAnterior.addEventListener('click', (ev) => {

    ev.stopPropagation();

    seleccionarLibroAdyacente(-1);
});

botonSiguiente.addEventListener('click', (ev) => {

    ev.stopPropagation();

    seleccionarLibroAdyacente(1);
});
document.addEventListener('keydown', (ev) => {

    if (!libroSeleccionado) return;

    // No interferir con el buscador
    if (
        ev.target.tagName === 'INPUT' ||
        ev.target.tagName === 'TEXTAREA' ||
        ev.target.tagName === 'SELECT'
    ) {
        return;
    }

    if (ev.key === 'ArrowLeft') {

        ev.preventDefault();
        seleccionarLibroAdyacente(-1);

    } else if (ev.key === 'ArrowRight') {

        ev.preventDefault();
        seleccionarLibroAdyacente(1);
    }
});
})();

