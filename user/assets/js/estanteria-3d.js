import * as THREE from 'three';

(async function () {
  const BASE = (window.APP_URL || '').replace(/\/$/, '');
  const API_BUSCAR = `${BASE}/user/backend/api/buscar_libros.php`;
  const API_FILTROS = `${BASE}/user/backend/api/filtros.php`;
  const TAMANO_PAGINA_LIBROS = 24;
  const parametrosURL = new URLSearchParams(window.location.search);
  const cantidadPrueba = Number(parametrosURL.get('prueba_rendimiento'));
  const modoPruebaRendimiento = window.PUEDE_PROBAR_RENDIMIENTO === true && [100, 200].includes(cantidadPrueba);

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
  const botonLeerPdf = document.getElementById('boton-leer-pdf');
  const botonLeerAnimado = document.getElementById('boton-leer-animado');
  const cargandoLibroEl = document.getElementById('cargando-libro');
  

  // ============================================================
  // LECTOR DE PDF
  // ============================================================

  let pdfjsListo = null;
  function obtenerPdfjs() {
    if (!pdfjsListo) {
      pdfjsListo = import('https://cdn.jsdelivr.net/npm/pdfjs-dist@4.0.379/build/pdf.min.mjs').then((modulo) => {
        modulo.GlobalWorkerOptions.workerSrc =
          'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.0.379/build/pdf.worker.min.mjs';
        window.pdfjsLib = modulo;
        return modulo;
      });
    }
    return pdfjsListo;
  }

const lectorPdf = document.getElementById('lector-pdf');
const cerrarLectorBtn = document.getElementById('cerrar-lector');
const libroAbiertoEl = document.getElementById('libro-abierto');
const canvasIzq = document.getElementById('canvas-pagina-izq');
const canvasDer = document.getElementById('canvas-pagina-der');
const btnPaginaAnterior = document.getElementById('pagina-anterior');
const btnPaginaSiguiente = document.getElementById('pagina-siguiente');
const lectorIndicador = document.getElementById('lector-indicador');
const btnVerUna = document.getElementById('ver-una-pagina');
const btnVerDos = document.getElementById('ver-dos-paginas');
const paginasInteriorEl = document.getElementById('paginas-interior');
const portadaFlipEl = document.getElementById('portada-flip');
const lomoFlipEl = document.getElementById('lomo-flip');
const portadaFlipImg = document.getElementById('portada-flip-img');
const portadaFlipFallback = document.getElementById('portada-flip-fallback');
const portadaFlipTitulo = document.getElementById('portada-flip-titulo');
const portadaFlipAutor = document.getElementById('portada-flip-autor');
const vueloContenedor = document.getElementById('vuelo-3d-contenedor');
// ============================================================
// ZOOM (lupa) sobre las páginas del lector
// ============================================================
const ZOOM_NIVEL = 2.2;
let zoomActivo = false;
let factorResolucionExtra = 1; // 1 = resolución normal; > 1 = zoom, más nítido
 
libroAbiertoEl.style.cursor = 'zoom-in';
 
paginasInteriorEl.addEventListener('click', (ev) => {
  alternarZoomLector(ev);
});
 
function alternarZoomLector(ev) {
  if (zoomActivo) {
    restaurarZoomLector();
    rerenderizarPaginasEnAltaResolucion(); // vuelve a la resolución normal
    return;
  }
 
  const rect = libroAbiertoEl.getBoundingClientRect();
  const origenX = ((ev.clientX - rect.left) / rect.width) * 100;
  const origenY = ((ev.clientY - rect.top) / rect.height) * 100;
 
  libroAbiertoEl.style.transformOrigin = `${origenX}% ${origenY}%`;
  libroAbiertoEl.style.transform = `scale(${ZOOM_NIVEL})`;
  libroAbiertoEl.style.cursor = 'zoom-out';
  libroAbiertoEl.style.zIndex = '10';
 
  zoomActivo = true;
 
  // Re-renderizamos el PDF con más resolución real, acorde al nivel
  // de zoom, así al agrandarlo con CSS se ve nítido y no borroso.
  factorResolucionExtra = ZOOM_NIVEL;
  rerenderizarPaginasEnAltaResolucion();
}
 
function restaurarZoomLector() {
  if (!zoomActivo) return;
  libroAbiertoEl.style.transform = 'scale(1)';
  libroAbiertoEl.style.cursor = 'zoom-in';
  zoomActivo = false;
 
  // Al no estar zoomeado no hace falta la resolución extra —
  // esto se aplica la próxima vez que se renderice una página
  // (por ejemplo dentro de renderizarPaginasActuales, que ya
  // llama a restaurarZoomLector() antes de renderizar).
  factorResolucionExtra = 1;
}

let escenaVuelo = null;
let camaraVuelo = null;
let rendererVuelo = null;
let mallaVuelo = null;

let pdfActual = null;
let totalPaginasPdf = 0;
let paginaIzqActual = 1;
let modoUnaPagina = window.matchMedia('(max-width: 768px)').matches;
let mallaOrigenLectura = null; // la malla 3D que se ocultó al abrir el lector

let rutaPdfActual = null; // qué PDF está abierto ahora, para guardar/leer su página
 
function claveUltimaPagina(ruta) {
  return `libro_ultima_pagina:${ruta}`;
}

function esMovilLector() {
  return window.matchMedia('(max-width: 768px)').matches;
}

function construirTapaHTML(libro) {
  if (libro.rutaFotoAbsoluta) {
    portadaFlipImg.src = libro.rutaFotoAbsoluta;
    portadaFlipImg.alt = `Portada de ${libro.titulo || 'libro'}`;
    portadaFlipImg.style.display = 'block';
    portadaFlipFallback.style.display = 'none';
  } else {
    portadaFlipImg.style.display = 'none';
    portadaFlipFallback.style.display = 'flex';
    portadaFlipFallback.style.background =
      `linear-gradient(160deg, ${libro.color}, ${sombrear(libro.color, -18)})`;
    portadaFlipTitulo.textContent = libro.titulo || '';
    portadaFlipAutor.textContent = libro.autor || '';
  }
}



function esperar(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}


function sincronizarAnchoPortada(anchoUnaPagina) {
  portadaFlipEl.style.width = `${anchoUnaPagina}px`;
  lomoFlipEl.style.width = `${anchoUnaPagina}px`; // mismo ancho: revela el bloque de páginas completo
}



function calcularTamanoDestino(aspectoPagina) {
  const margenVertical = 140; // deja espacio para controles arriba/abajo
  const altoMax = window.innerHeight - margenVertical;
  const anchoMax = window.innerWidth * 1;

  if (modoUnaPagina) {
    let alto = altoMax;
    let ancho = alto * aspectoPagina;
    if (ancho > anchoMax) {
      ancho = anchoMax;
      alto = ancho / aspectoPagina;
    }
    return { anchoDestino: ancho, altoDestino: alto };
  } else {
    // dos hojas lado a lado: el ancho total es el doble del de una página
    let alto = altoMax;
    let ancho = alto * aspectoPagina * 2;
    if (ancho > anchoMax) {
      ancho = anchoMax;
      alto = ancho / (aspectoPagina * 2);
    }
    return { anchoDestino: ancho, altoDestino: alto };
  }
}

function obtenerRectPantalla(malla) {
  // Reutiliza la lógica de proyección que ya usás en actualizarBotonInfoPosicion,
  // pero tomando las 4 esquinas de la portada para un rect completo.
  const estante = obtenerEstanteDeMallaGlobal(malla);
  if (!estante || !estante.renderer) return null;

  const caja = new THREE.Box3().setFromObject(malla);
  const esquinaSupIzq = new THREE.Vector3(caja.min.x, caja.max.y, caja.max.z).project(estante.camara);
  const esquinaInfDer = new THREE.Vector3(caja.max.x, caja.min.y, caja.max.z).project(estante.camara);

  const rectCanvas = estante.renderer.domElement.getBoundingClientRect();

  const x1 = ((esquinaSupIzq.x + 1) / 2) * rectCanvas.width + rectCanvas.left;
  const y1 = ((-esquinaSupIzq.y + 1) / 2) * rectCanvas.height + rectCanvas.top;
  const x2 = ((esquinaInfDer.x + 1) / 2) * rectCanvas.width + rectCanvas.left;
  const y2 = ((-esquinaInfDer.y + 1) / 2) * rectCanvas.height + rectCanvas.top;

  return {
    top: Math.min(y1, y2),
    left: Math.min(x1, x2),
    width: Math.abs(x2 - x1),
    height: Math.abs(y2 - y1),
  };
}

// Necesitamos poder encontrar a qué estante pertenece una malla desde
// fuera de la clase Estante — usamos el mismo array global "estantes"
// (y estanteResultados si existe).
function obtenerEstanteDeMallaGlobal(malla) {
  const todos = estanteResultados ? [...estantes, estanteResultados] : estantes;
  return todos.find((e) => e.libros.includes(malla)) || null;
}

async function renderizarPaginasActuales() {
 
  restaurarZoomLector();
 
  if (!pdfActual) return;
 
  await renderizarPaginaEnCanvas(paginaIzqActual, canvasIzq);
 
  if (!modoUnaPagina && paginaIzqActual + 1 <= totalPaginasPdf) {
    canvasDer.style.visibility = 'visible';
    await renderizarPaginaEnCanvas(paginaIzqActual + 1, canvasDer);
  } else {
    canvasDer.style.visibility = 'hidden';
  }
 
  actualizarIndicadorYBotones();
 
  // Guardamos en qué página quedó ESTE libro, para retomarlo la
  // próxima vez que se abra.
  if (rutaPdfActual) {
    try {
      localStorage.setItem(claveUltimaPagina(rutaPdfActual), String(paginaIzqActual));
    } catch (e) {
      // idem arriba: si falla, no pasa nada, simplemente no se recuerda.
    }
  }
}

async function rerenderizarPaginasEnAltaResolucion() {
  if (!pdfActual) return;
 
  await renderizarPaginaEnCanvas(paginaIzqActual, canvasIzq);
 
  if (!modoUnaPagina && paginaIzqActual + 1 <= totalPaginasPdf) {
    await renderizarPaginaEnCanvas(paginaIzqActual + 1, canvasDer);
  }
}

async function renderizarPaginaEnCanvas(numeroPagina, canvas) {
  if (numeroPagina < 1 || numeroPagina > totalPaginasPdf) return;

  const pagina = await pdfActual.getPage(numeroPagina);
  const contenedor = canvas.parentElement;
  const escalaBase = pagina.getViewport({ scale: 1 });
  const rectContenedor = contenedor.getBoundingClientRect();
  const anchoDisponible = contenedor.clientWidth || rectContenedor.width;
  const altoDisponible = contenedor.clientHeight || rectContenedor.height;
  if (!anchoDisponible || !altoDisponible) return;

  const dpr = window.devicePixelRatio || 1;
  const escala = Math.min(
    anchoDisponible / escalaBase.width,
    altoDisponible / escalaBase.height
  ) * dpr * factorResolucionExtra;
  if (!isFinite(escala) || escala <= 0) return;

  const viewport = pagina.getViewport({ scale: escala });

  // Renderizamos en un canvas OFFSCREEN, invisible para el usuario.
  const bufferCanvas = document.createElement('canvas');
  bufferCanvas.width = Math.round(viewport.width);
  bufferCanvas.height = Math.round(viewport.height);
  await pagina.render({
    canvasContext: bufferCanvas.getContext('2d'),
    viewport,
  }).promise;

  // Recién cuando terminó, volcamos todo de una sola vez al canvas visible.
  // Así el canvas visible nunca pasa por un estado "en blanco".
  canvas.width = bufferCanvas.width;
  canvas.height = bufferCanvas.height;
  canvas.style.width = `${viewport.width / dpr}px`;
  canvas.style.height = `${viewport.height / dpr}px`;
  canvas.getContext('2d').drawImage(bufferCanvas, 0, 0);
}



function actualizarIndicadorYBotones() {
  if (modoUnaPagina) {
    lectorIndicador.textContent = `${paginaIzqActual} / ${totalPaginasPdf}`;
    btnPaginaAnterior.disabled = paginaIzqActual <= 1;
    btnPaginaSiguiente.disabled = paginaIzqActual >= totalPaginasPdf;
  } else {
    const finRango = Math.min(paginaIzqActual + 1, totalPaginasPdf);
    lectorIndicador.textContent = `${paginaIzqActual}-${finRango} / ${totalPaginasPdf}`;
    btnPaginaAnterior.disabled = paginaIzqActual <= 1;
    btnPaginaSiguiente.disabled = paginaIzqActual + 1 >= totalPaginasPdf;
  }
}

function actualizarModoVista() {
  paginasInteriorEl.classList.toggle('modo-una-pagina', modoUnaPagina);
  btnVerUna.classList.toggle('activo', modoUnaPagina);
  btnVerDos.classList.toggle('activo', !modoUnaPagina);
}

btnPaginaAnterior.addEventListener('click', async () => {
  const salto = modoUnaPagina ? 1 : 2;
  paginaIzqActual = Math.max(1, paginaIzqActual - salto);
  await renderizarPaginasActuales();
});

btnPaginaSiguiente.addEventListener('click', async () => {
  const salto = modoUnaPagina ? 1 : 2;
  paginaIzqActual = Math.min(totalPaginasPdf, paginaIzqActual + salto);
  await renderizarPaginasActuales();
});

btnVerUna.addEventListener('click', async () => {
  if (esMovilLector()) return;
  modoUnaPagina = true;
  actualizarModoVista();
  await recalcularTamanoYRenderizar();
});

btnVerDos.addEventListener('click', async () => {
  if (esMovilLector()) return;
  modoUnaPagina = false;
  actualizarModoVista();
  await recalcularTamanoYRenderizar();
});

async function recalcularTamanoYRenderizar() {
  if (!pdfActual) return;

  const pagina = await pdfActual.getPage(1);
  const vp = pagina.getViewport({ scale: 1 });
  const aspecto = vp.width / vp.height;

  const altoDestino = parseFloat(libroAbiertoEl.style.height) || window.innerHeight * 0.6;
  const anchoCubierta = altoDestino * aspecto;
  const anchoFinalInterior = modoUnaPagina ? anchoCubierta : anchoCubierta * 2;

  libroAbiertoEl.style.top = `${(window.innerHeight - altoDestino) / 2}px`;
  libroAbiertoEl.style.left = `${(window.innerWidth - anchoFinalInterior) / 2}px`;
  libroAbiertoEl.style.width = `${anchoFinalInterior}px`;
  libroAbiertoEl.style.height = `${altoDestino}px`;

  // AGREGAR:
  portadaFlipEl.style.width = `${anchoCubierta}px`;
  lomoFlipEl.style.width = `${anchoCubierta}px`;

  await renderizarPaginasActuales();
}

function cerrarLector() {

  cargandoLibroEl.classList.remove('visible'); // ← LÍNEA NUEVA

  restaurarZoomLector();
  salirDePantallaCompleta();

  portadaFlipEl.classList.remove('oculta');
  portadaFlipEl.style.transition = 'none';
  portadaFlipEl.style.transform = 'rotateY(0deg)';
  portadaFlipEl.offsetHeight;
  portadaFlipEl.style.transition = '';

  lectorPdf.classList.remove('visible');

  if (mallaOrigenLectura) {
    mallaOrigenLectura.visible = true;
    mallaOrigenLectura = null;
  }

  pdfActual = null;
  totalPaginasPdf = 0;
}

  cerrarLectorBtn.addEventListener('click', cerrarLector);

  document.addEventListener('keydown', (ev) => {
    if (!lectorPdf.classList.contains('visible')) return;
    if (ev.key === 'Escape') cerrarLector();
    if (ev.key === 'ArrowLeft') btnPaginaAnterior.click();
    if (ev.key === 'ArrowRight') btnPaginaSiguiente.click();
  });

  let datosFiltros = { categorias: [], subcategorias: [], tipos: [], idiomas: [], temas: [] };
  let temasSeleccionados = [];
  let temasTemporales = [];

  const ALTO_LIBRO = 1.55;
  const ALTO_LIBRO_DESTINO = 720;
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

  const ESCALA_LOMO = 480;
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

  const ESCALA_PORTADA = 480;
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
      this._tokenPintura = 0;
      this.id = categoria.id;
      this.nombre = categoria.nombre;
      this.datosLibros = [];
      this.totalLibros = 0;
      this.cargarPagina = null;
      this.tokenPagina = 0;
      this.datosPrueba = null;
      this.gruposPrueba = null;
      this.paginaLibros = 0;
      this.librosPorPagina = 24;

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

      const puntosGrupos = document.createElement('div');
      puntosGrupos.className = 'puntos-grupos-libros';
      puntosGrupos.setAttribute('role', 'group');
      puntosGrupos.setAttribute('aria-label', `Grupos de libros de ${this.nombre}`);
      this.puntosGrupos = puntosGrupos;
      this.btnGrupoAnterior = document.createElement('button');
      this.btnGrupoAnterior.type = 'button';
      this.btnGrupoAnterior.className = 'flecha-grupo-libros';
      this.btnGrupoAnterior.textContent = '‹';
      this.btnGrupoAnterior.setAttribute('aria-label', 'Grupo anterior');
      this.btnGrupoSiguiente = document.createElement('button');
      this.btnGrupoSiguiente.type = 'button';
      this.btnGrupoSiguiente.className = 'flecha-grupo-libros';
      this.btnGrupoSiguiente.textContent = '›';
      this.btnGrupoSiguiente.setAttribute('aria-label', 'Grupo siguiente');

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
      const navegacionGrupos = document.createElement('div');
      navegacionGrupos.className = 'navegacion-grupos-libros';
      navegacionGrupos.append(this.btnGrupoAnterior, puntosGrupos, this.btnGrupoSiguiente);
      seccion.append(titulo, lienzo, navegacionGrupos);
      contenedorPrincipal.appendChild(seccion);

      this.seccionEl = seccion;
      this.lienzoEl = lienzo;
      this.mensajeVacioEl = mensajeVacio;
      this.botonInfoEl = botonInfo;
      this.controlesEl = controles;
      this.btnAnteriorEl = btnAnt;
      this.btnSiguienteEl = btnSig;

      this._dibujarPuntosGrupos();
      this.btnGrupoAnterior.addEventListener('click', () => this.irAGrupo(this.paginaLibros - 1));
      this.btnGrupoSiguiente.addEventListener('click', () => this.irAGrupo(this.paginaLibros + 1));

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
      this.escena.background = null;

      this.camara = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 100);
      this.camara.position.set(0, ALTO_LIBRO / 2, 5);

      this.renderer = new THREE.WebGLRenderer({ antialias: false, alpha: true, powerPreference: 'low-power' });
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
      this._tokenPintura++; // <-- AGREGAR: invalida cualquier _pintar() en curso

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
      this.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
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
      await this.establecerPagina(libros, libros.length, 0);
    }

    establecerTotal(total) {
      this.totalLibros = Math.max(0, Number(total) || 0);
      this.paginaLibros = Math.min(
        this.paginaLibros,
        Math.max(0, Math.ceil(this.totalLibros / this.librosPorPagina) - 1)
      );
      this._actualizarTitulo(this.totalLibros);
      this._dibujarPuntosGrupos();
    }

    prepararGruposPrueba() {
      if (!this.datosPrueba) return;
      const ancho = this.lienzoEl.clientWidth || 960;
      // La cámara encuadra la altura del libro; usar el ancho real del marco
      // en unidades de mundo evita asumir un número fijo por grupo.
      const altoMundo = window.matchMedia('(max-width: 768px)').matches ? 1.6 : 1.58;
      const anchoMundo = altoMundo * (ancho / (this.lienzoEl.clientHeight || 480));
      this.gruposPrueba = [];
      let grupo = [], ocupado = 0;
      for (const libro of this.datosPrueba) {
        const w = grosorPorPaginas(libro.paginas) + SEPARACION;
        if (grupo.length && ocupado + w > anchoMundo) {
          this.gruposPrueba.push(grupo);
          grupo = [];
          ocupado = 0;
        }
        grupo.push(libro);
        ocupado += w;
      }
      if (grupo.length) this.gruposPrueba.push(grupo);
      this.librosPorPagina = Math.max(1, ...this.gruposPrueba.map((g) => g.length));
    }

    obtenerGrupo(pagina) {
      if (this.gruposPrueba) return this.gruposPrueba[pagina] || [];
      const inicio = pagina * this.librosPorPagina;
      return this.datosPrueba.slice(inicio, inicio + this.librosPorPagina);
    }

    async establecerPagina(libros, total = this.totalLibros, pagina = this.paginaLibros) {
      const paginaAnterior = this.paginaLibros;
      const cambiarGrupo = pagina !== paginaAnterior && this.libros.length > 0;
      const direccion = pagina > paginaAnterior ? 1 : -1;
      if (cambiarGrupo) await this._animarSalidaGrupo();
      this.datosLibros = libros;
      this.paginaLibros = pagina;
      this.totalLibros = Math.max(0, Number(total) || 0);
      this._actualizarTitulo(this.totalLibros);
      this._dibujarPuntosGrupos();
      if (this.renderer) await this._pintar(libros, cambiarGrupo ? direccion : 0);
    }

    _animarSalidaGrupo() {
      const inicio = performance.now();
      const duracion = 220;
      const posiciones = this.libros.map((libro) => libro.position.x);
      return new Promise((resolve) => {
        const paso = (ahora) => {
          const progreso = Math.min(1, (ahora - inicio) / duracion);
          const suavizado = progreso * progreso * (3 - 2 * progreso);
          this.libros.forEach((libro, indice) => {
            libro.position.x = posiciones[indice] - 5 * suavizado;
            libro.scale.setScalar(1 - 0.12 * suavizado);
          });
          if (progreso < 1) requestAnimationFrame(paso);
          else resolve();
        };
        requestAnimationFrame(paso);
      });
    }

    _dibujarPuntosGrupos() {
      const cantidad = this.gruposPrueba ? this.gruposPrueba.length : Math.ceil(this.totalLibros / this.librosPorPagina);
      this.puntosGrupos.replaceChildren();
      this.puntosGrupos.hidden = cantidad <= 1;
      this.btnGrupoAnterior.hidden = cantidad <= 1;
      this.btnGrupoSiguiente.hidden = cantidad <= 1;
      this.btnGrupoAnterior.disabled = this.paginaLibros <= 0;
      this.btnGrupoSiguiente.disabled = this.paginaLibros >= cantidad - 1;
      for (let pagina = 0; pagina < cantidad; pagina++) {
        const punto = document.createElement('button');
        punto.type = 'button';
        punto.className = 'punto-grupo-libros';
        punto.setAttribute('aria-label', `Mostrar grupo ${pagina + 1} de ${cantidad}`);
        punto.setAttribute('aria-current', pagina === this.paginaLibros ? 'true' : 'false');
        punto.title = `Grupo ${pagina + 1} de ${cantidad}`;
        punto.addEventListener('click', () => {
          if (pagina === this.paginaLibros) return;
          if (this.datosPrueba) {
            this.establecerPagina(this.obtenerGrupo(pagina), this.totalLibros, pagina);
          } else if (this.cargarPagina) {
            this.cargarPagina(pagina);
          }
        });
        this.puntosGrupos.appendChild(punto);
      }
    }

    async irAGrupo(pagina, seleccionarBorde = null) {
      const cantidad = this.gruposPrueba ? this.gruposPrueba.length : Math.ceil(this.totalLibros / this.librosPorPagina);
      if (pagina < 0 || pagina >= cantidad || pagina === this.paginaLibros) return;
      if (this.datosPrueba) {
        await this.establecerPagina(this.obtenerGrupo(pagina), this.totalLibros, pagina);
      } else if (this.cargarPagina) {
        await this.cargarPagina(pagina);
      }
      if (seleccionarBorde && this.libros.length) this.seleccionar(seleccionarBorde === 'ultimo' ? this.libros.at(-1) : this.libros[0]);
    }

    async _pintar(libros, direccionEntrada = 0) {
      const miToken = ++this._tokenPintura;
      const librosVisibles = libros.slice(0, this.librosPorPagina);

      // Mostrar spinner ANTES de tocar nada
      this.cargandoEl.classList.remove('oculto');

      // Limpiar libros anteriores (esto es síncrono, siempre seguro de hacer)
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
        this.cargandoEl.classList.add('oculto');
        return;
      }
      this.mensajeVacioEl.style.display = 'none';

      for (const libro of librosVisibles) {
        // Si mientras esperábamos la textura llegó una búsqueda más nueva
        // para este mismo estante, abandonamos — no seguimos agregando
        // libros de una lista que ya quedó obsoleta.
        if (miToken !== this._tokenPintura) return;

        const rutaFotoAbsoluta = libro.ruta_foto
          ? `${BASE}/${libro.ruta_foto}`.replace(/([^:]\/)\/+/g, '$1')
          : null;
        const malla = await crearLibro3D({ ...libro, rutaFotoAbsoluta });

        if (miToken !== this._tokenPintura) return; // se volvió obsoleta mientras cargaba la textura

        this.escena.add(malla);
        this.libros.push(malla);
      }

      if (miToken !== this._tokenPintura) return;

      this._acomodarEnFila();
      if (direccionEntrada !== 0) await this._animarEntradaGrupo(direccionEntrada);
      this.cargandoEl.classList.add('oculto');
    }

    _animarEntradaGrupo(direccion) {
      const inicio = performance.now();
      const duracion = 260;
      const destinos = this.libros.map((libro) => libro.position.x);
      this.libros.forEach((libro, indice) => {
        libro.position.x = destinos[indice] + 5 * direccion;
      });
      return new Promise((resolve) => {
        const paso = (ahora) => {
          const progreso = Math.min(1, (ahora - inicio) / duracion);
          const suavizado = 1 - Math.pow(1 - progreso, 3);
          this.libros.forEach((libro, indice) => {
            libro.position.x = destinos[indice] + 5 * direccion * (1 - suavizado);
          });
          if (progreso < 1) requestAnimationFrame(paso);
          else resolve();
        };
        requestAnimationFrame(paso);
      });
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
      const cantidadGrupos = this.gruposPrueba ? this.gruposPrueba.length : Math.ceil(this.totalLibros / this.librosPorPagina);
      if (nuevoIndice < 0) {
        if (this.paginaLibros > 0) this.irAGrupo(this.paginaLibros - 1, 'ultimo');
        return;
      }
      if (nuevoIndice >= this.libros.length) {
        if (this.paginaLibros < cantidadGrupos - 1) this.irAGrupo(this.paginaLibros + 1, 'primero');
        return;
      }
      this.seleccionar(this.libros[nuevoIndice]);
    }

    _actualizarControles() {
      if (!this.libroSeleccionado) {
        this.controlesEl.style.display = 'none';
        return;
      }
      this.controlesEl.style.display = 'flex';
      const indice = this.libros.indexOf(this.libroSeleccionado);
      const inicioGrupo = this.gruposPrueba
        ? this.gruposPrueba.slice(0, this.paginaLibros).reduce((n, grupo) => n + grupo.length, 0)
        : this.paginaLibros * this.librosPorPagina;
      const indiceAbsoluto = inicioGrupo + indice;
      this.btnAnteriorEl.disabled = indiceAbsoluto <= 0;
      this.btnSiguienteEl.disabled = indiceAbsoluto >= this.totalLibros - 1;
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
  let tokenBusqueda = 0;
  let resumenCategorias = new Map();

  const observadorVisibilidad = new IntersectionObserver(
    (entradas) => {
      entradas.forEach((entrada) => {
        const estante = estantes.find((e) => e.seccionEl === entrada.target);
        if (!estante) return;
        if (entrada.isIntersecting) {
          estante.inicializar();
          if (!estante.datosPrueba && estante.datosLibros.length === 0 && estante.cargarPagina) {
            estante.cargarPagina(estante.paginaLibros);
          }
        } else {
          estante.destruir();
        }
      });
    },
    // Mantiene pocos contextos WebGL activos: solo prepara la categoría visible.
    { root: null, rootMargin: '0px', threshold: 0 }
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
    estante.librosPorPagina = TAMANO_PAGINA_LIBROS;
    estante.cargarPagina = (pagina) => cargarPaginaCategoria(estante, pagina);
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
      boton.textContent = `${estante.nombre} (${estante.totalLibros})`;
      boton.addEventListener('click', () => {
        estante.seccionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
      cont.appendChild(boton);
    });
  }

  // ============================================================
  // Loop de render único: recorre solo los estantes con renderer activo
  // ============================================================
  let ultimoFrameRender = 0;
  (function animar(tiempo) {
  requestAnimationFrame(animar);
  if (document.hidden || tiempo - ultimoFrameRender < 1000 / 30) return;
  ultimoFrameRender = tiempo;

  const activos = estanteResultados ? [...estantes, estanteResultados] : estantes;
  activos.forEach((estante) => {
    if (!estante.renderer) return;
    estante.animarDesplazamientoSuave();
    estante.actualizarBotonInfoPosicion();
    estante.renderer.render(estante.escena, estante.camara);
  });

  // ← agregar esto:
  if (rendererVuelo && vueloContenedor.classList.contains('visible')) {
    rendererVuelo.render(escenaVuelo, camaraVuelo);
  }
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
      const rutaPdfAbsoluta = `${BASE}/${libro.ruta_pdf}`.replace(/([^:]\/)\/+/g, '$1');

      // Botón "LEER PDF" → abre el archivo directo en pestaña nueva
      botonLeerPdf.href = rutaPdfAbsoluta;
      botonLeerPdf.style.display = 'flex';

      // Botón "LEER" → dispara la animación del lector 3D
      botonLeerAnimado.style.display = 'flex';
      botonLeerAnimado.onclick = async () => {
      const malla = seleccionGlobal ? seleccionGlobal.malla : null;
      infoLibro.classList.remove('visible');

      await solicitarPantallaCompleta();

      // Un par de frames de margen: el fullscreen puede "resolver" la
      // promesa un instante antes de que innerWidth/innerHeight ya estén
      // actualizados del todo — esto evita esa carrera.
      await new Promise((resolve) => {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
      });

      volarLibroYAbrir(rutaPdfAbsoluta, malla);
    };
    } else {
      botonLeerPdf.removeAttribute('href');
      botonLeerPdf.style.display = 'none';

      botonLeerAnimado.onclick = null;
      botonLeerAnimado.style.display = 'none';
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

  function crearLibrosPrueba(cantidad) {
    const colores = ['#70402E', '#245C68', '#775C2E', '#4E4772', '#48613D', '#873F55'];
    return Array.from({ length: cantidad }, (_, indice) => ({
      id: `prueba-${indice + 1}`,
      titulo: `Libro de prueba ${String(indice + 1).padStart(3, '0')}`,
      autor: `Autor de prueba ${indice % 12 + 1}`,
      descripcion: 'Registro sintético para revisar la carga por grupos. No se guarda en la base de datos.',
      idioma: 'Español', disponibilidad: 1, temas: 'Prueba', tipo: 'Prueba', edicion: '1',
      ano: '2026', cdd: '', bloque: '', categoria: 'Prueba de rendimiento', subcategoria: '',
      seccion: '', editorial: 'Datos sintéticos', ejemplares: 1, prestados: 0,
      paginas: 100 + (indice % 300), color: colores[indice % colores.length],
      ruta_foto: null, ruta_pdf: null,
    }));
  }

  async function solicitarPagina(params, pagina) {
    const consulta = new URLSearchParams(params);
    consulta.set('limite', String(TAMANO_PAGINA_LIBROS));
    consulta.set('offset', String(pagina * TAMANO_PAGINA_LIBROS));
    const respuesta = await fetch(`${API_BUSCAR}?${consulta.toString()}`);
    if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
    return {
      libros: await respuesta.json(),
      total: Number(respuesta.headers.get('X-Total-Count')) || 0,
    };
  }

  async function cargarPaginaCategoria(estante, pagina) {
    const token = ++estante.tokenPagina;
    estante.cargandoEl.classList.remove('oculto');
    try {
      if (estante.datosPrueba) {
        const desde = pagina * TAMANO_PAGINA_LIBROS;
        await estante.establecerPagina(estante.datosPrueba.slice(desde, desde + TAMANO_PAGINA_LIBROS), estante.totalLibros, pagina);
        return;
      }
      const params = new URLSearchParams({ categoria: estante.nombre });
      const resultado = await solicitarPagina(params, pagina);
      if (token !== estante.tokenPagina) return;
      await estante.establecerPagina(resultado.libros, resultado.total, pagina);
    } catch (err) {
      if (token === estante.tokenPagina) {
        console.error(`No se pudo cargar ${estante.nombre}`, err);
        estante.mensajeVacioEl.textContent = 'No se pudieron cargar los libros.';
        estante.mensajeVacioEl.style.display = 'flex';
      }
    } finally {
      if (token === estante.tokenPagina) estante.cargandoEl.classList.add('oculto');
    }
  }

  async function buscarLibros() {
    const miToken = ++tokenBusqueda;
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
      if (estantes.length === 0) {
        const categorias = [...datosFiltros.categorias];
        if (modoPruebaRendimiento) categorias.push({ id: 'prueba-rendimiento', nombre: 'Prueba de rendimiento' });
        crearEstantesCategorias(categorias);
      }

      if (!hayFiltrosActivos()) {
        estantes.forEach((estante) => {
          if (estante.datosPrueba) return;
          estante.tokenPagina++;
          estante.datosLibros = [];
          estante.paginaLibros = 0;
          estante.paginaLibros = 0;
          estante._dibujarPuntosGrupos();
        });
        mostrarModoCategorias();
        const respuestaResumen = await fetch(`${API_BUSCAR}?resumen=1`);
        if (!respuestaResumen.ok) throw new Error(`HTTP ${respuestaResumen.status}`);
        const resumen = await respuestaResumen.json();
        if (miToken !== tokenBusqueda) return;
        resumenCategorias = new Map(resumen.map((fila) => [fila.categoria, Number(fila.total)]));
        for (const estante of estantes) {
          if (estante.datosPrueba) {
            estante.prepararGruposPrueba();
            estante.establecerTotal(cantidadPrueba);
            const datosPagina = estante.obtenerGrupo(0);
            await estante.establecerPagina(datosPagina, cantidadPrueba, 0);
          } else {
            estante.establecerTotal(resumenCategorias.get(estante.nombre) || 0);
          }
        }
        construirTabsCategorias();
        const totalCatalogo = resumen.reduce((suma, fila) => suma + Number(fila.total), 0) + (modoPruebaRendimiento ? cantidadPrueba : 0);
        sinResultados.style.display = totalCatalogo === 0 ? 'block' : 'none';
        sinResultados.textContent = 'No se encontraron libros.';
        ocultarCarga();
        return;
      }

      const { libros, total } = await solicitarPagina(params, 0);
      if (miToken !== tokenBusqueda) return;
      await mostrarModoResultados();
      if (miToken !== tokenBusqueda) return;
      const estante = estanteResultados;
      estante.cargarPagina = async (pagina) => {
        const tokenPagina = ++estante.tokenPagina;
        estante.cargandoEl.classList.remove('oculto');
        try {
          const paginaResultado = await solicitarPagina(params, pagina);
          if (tokenPagina === estante.tokenPagina) {
            await estante.establecerPagina(paginaResultado.libros, paginaResultado.total, pagina);
          }
        } catch (err) {
          console.error('No se pudo cargar la página de resultados', err);
        } finally {
          if (tokenPagina === estante.tokenPagina) estante.cargandoEl.classList.add('oculto');
        }
      };
      await estante.establecerPagina(libros, total, 0);
      sinResultados.style.display = total === 0 ? 'block' : 'none';
      sinResultados.textContent = 'No se encontraron libros.';
      ocultarCarga();
    } catch (err) {
      if (miToken !== tokenBusqueda) return; // error de una búsqueda ya obsoleta, ignorar

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
  const categoriasCatalogo = [...datosFiltros.categorias];
  if (modoPruebaRendimiento) {
    categoriasCatalogo.push({ id: 'prueba-rendimiento', nombre: 'Prueba de rendimiento' });
  }
  crearEstantesCategorias(categoriasCatalogo);
  if (modoPruebaRendimiento) {
    const estantePrueba = estantes.find((estante) => estante.id === 'prueba-rendimiento');
    if (estantePrueba) estantePrueba.datosPrueba = crearLibrosPrueba(cantidadPrueba);
  }
  aplicarFiltrosDesdeURL(); // <-- NUEVO: lee ?categoria=...&tema=... antes de buscar
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

function aplicarFiltrosDesdeURL() {
    const params = new URLSearchParams(window.location.search);

    const categoriaURL = params.get('categoria');
    const subcategoriaURL = params.get('subcategoria');
    const tipoURL = params.get('tipo');
    const idiomaURL = params.get('idioma');
    const disponibilidadURL = params.get('disponibilidad');
    const buscarURL = params.get('buscar');
    const temaURL = params.get('tema'); // nombre de un solo tema

    let huboCambios = false;

    if (categoriaURL) {
      // Verificamos que exista esa categoría entre las opciones cargadas
      const existe = datosFiltros.categorias.some((c) => c.nombre === categoriaURL);
      if (existe) {
        selCat.value = categoriaURL;
        actualizarSubcategorias(); // porque cambiar categoría repuebla el select de subcategoría
        huboCambios = true;
      }
    }

    if (subcategoriaURL) {
      // Solo tiene sentido si ya se aplicó la categoría (el <select> ya está poblado)
      const opcionExiste = [...selSubcat.options].some((o) => o.value === subcategoriaURL);
      if (opcionExiste) {
        selSubcat.value = subcategoriaURL;
        huboCambios = true;
      }
    }

    if (tipoURL) {
      const existe = datosFiltros.tipos.some((t) => t.nombre === tipoURL);
      if (existe) { selTipo.value = tipoURL; huboCambios = true; }
    }

    if (idiomaURL) {
      const existe = datosFiltros.idiomas.includes(idiomaURL);
      if (existe) { selIdioma.value = idiomaURL; huboCambios = true; }
    }

    if (disponibilidadURL === '1' || disponibilidadURL === '0') {
      selDisponibilidad.value = disponibilidadURL;
      huboCambios = true;
    }

    if (buscarURL) {
      inputBuscar.value = buscarURL;
      huboCambios = true;
    }

    if (temaURL) {
      const tema = datosFiltros.temas.find((t) => t.nombre === temaURL);
      if (tema) {
        temasSeleccionados = [Number(tema.id)];
        actualizarTextoTemas();
        huboCambios = true;
      }
    }

    return huboCambios;
  }


function proyectarCajaAPantalla(malla, camara, renderer) {
  const caja = new THREE.Box3().setFromObject(malla);
  const esqSupIzq = new THREE.Vector3(caja.min.x, caja.max.y, caja.max.z).project(camara);
  const esqInfDer = new THREE.Vector3(caja.max.x, caja.min.y, caja.max.z).project(camara);

  const rectCanvas = renderer.domElement.getBoundingClientRect();

  const x1 = ((esqSupIzq.x + 1) / 2) * rectCanvas.width + rectCanvas.left;
  const y1 = ((-esqSupIzq.y + 1) / 2) * rectCanvas.height + rectCanvas.top;
  const x2 = ((esqInfDer.x + 1) / 2) * rectCanvas.width + rectCanvas.left;
  const y2 = ((-esqInfDer.y + 1) / 2) * rectCanvas.height + rectCanvas.top;

  return {
    top: Math.min(y1, y2),
    left: Math.min(x1, x2),
    width: Math.abs(x2 - x1),
    height: Math.abs(y2 - y1),
  };
}


function extraerColorDominante(img) {
  const canvas = document.createElement('canvas');
  // Reducimos la imagen para que el muestreo sea rápido
  const w = canvas.width = 32;
  const h = canvas.height = 32;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(img, 0, 0, w, h);

  let r = 0, g = 0, b = 0, total = 0;
  const { data } = ctx.getImageData(0, 0, w, h);

  for (let i = 0; i < data.length; i += 4) {
    const alpha = data[i + 3];
    if (alpha < 200) continue; // ignorar píxeles muy transparentes
    r += data[i];
    g += data[i + 1];
    b += data[i + 2];
    total++;
  }

  if (total === 0) return '#8C7355'; // fallback
  r = Math.round(r / total);
  g = Math.round(g / total);
  b = Math.round(b / total);

  return `rgb(${r},${g},${b})`;
}

function cargarImagen(src) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.crossOrigin = 'anonymous'; // OJO: el servidor debe mandar CORS habilitado
    img.onload = () => resolve(img);
    img.onerror = reject;
    img.src = src;
  });
}

/* ==================================================================
   PARCHE — animación de vuelo del libro + apertura del lector
   ==================================================================
   Instrucciones:

   1) BORRÁ del archivo original las declaraciones duplicadas de:
        - calcularTamanoCubierta (aparece 2 veces)
        - construirPortadaHTML   (aparece 2 veces)
      Dejá solo UNA versión de cada una (usá las de más abajo,
      que sí setean --color-lomo).

   2) BORRÁ por completo la función asegurarRendererVuelo() original.
      Reemplazala por crearRendererVuelo() / destruirRendererVuelo()
      de acá abajo.

   3) Reemplazá tu función volarLibroYAbrir() completa por la
      versión de acá abajo.
   ================================================================== */


// ------------------------------------------------------------------
// (1) Únicas versiones de estas dos funciones — reemplazan a las
//     dos copias duplicadas que tenías en el archivo original.
// ------------------------------------------------------------------

function calcularTamanoCubierta(aspectoPagina) {
  const margenVertical = 140;
  const altoMax = window.innerHeight - margenVertical;
  const anchoMax = window.innerWidth * 0.5; // una sola página no pasa de ~42% del ancho

  let alto = altoMax;
  let ancho = alto * aspectoPagina;
  if (ancho > anchoMax) {
    ancho = anchoMax;
    alto = ancho / aspectoPagina;
  }
  return { anchoCubierta: ancho, altoDestino: alto };
}

function construirPortadaHTML(libro) {
  if (libro.rutaFotoAbsoluta) {
    portadaFlipImg.src = libro.rutaFotoAbsoluta;
    portadaFlipImg.alt = `Portada de ${libro.titulo || 'libro'}`;
    portadaFlipImg.style.display = 'block';
    portadaFlipFallback.style.display = 'none';
  } else {
    portadaFlipImg.style.display = 'none';
    portadaFlipFallback.style.display = 'flex';
    portadaFlipFallback.style.background =
      `linear-gradient(160deg, ${libro.color}, ${sombrear(libro.color, -18)})`;
    portadaFlipTitulo.textContent = libro.titulo || '';
    portadaFlipAutor.textContent = libro.autor || '';
  }
  // Esta línea es la que faltaba en tu segunda copia duplicada:
  // sin ella el lomo se quedaba con el color por defecto.
}


// ------------------------------------------------------------------
// (2) Renderer de vuelo: se crea justo antes de animar y se destruye
//     apenas termina la animación, tal como pediste — así no queda
//     un WebGLRenderer + canvas vivos consumiendo recursos entre
//     una lectura y la siguiente.
// ------------------------------------------------------------------

function crearRendererVuelo() {
  // Por las dudas, si quedó uno vivo de una animación anterior
  // que no se cerró bien, lo destruimos antes de crear el nuevo.
  if (rendererVuelo) destruirRendererVuelo();

  escenaVuelo = new THREE.Scene();
  camaraVuelo = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 100);
  camaraVuelo.position.set(0, 0, 5);

  rendererVuelo = new THREE.WebGLRenderer({ antialias: false, alpha: true, powerPreference: 'low-power' });
  rendererVuelo.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
  vueloContenedor.appendChild(rendererVuelo.domElement);
}

function destruirRendererVuelo() {
  if (!rendererVuelo) return;

  if (mallaVuelo) {
    escenaVuelo.remove(mallaVuelo);
    // OJO: acá solo destruimos los MATERIALES CLONADOS del vuelo.
    // NO tocamos mat.map (las texturas), porque esas texturas son
    // las mismas que sigue usando el libro original en la estantería.
    mallaVuelo.material.forEach((mat) => mat.dispose());
    mallaVuelo = null;
  }

  rendererVuelo.dispose();
  rendererVuelo.domElement.remove();
  rendererVuelo = null;
  escenaVuelo = null;
  camaraVuelo = null;
}


// ------------------------------------------------------------------
// (3) volarLibroYAbrir corregida:
//     - crea el renderer de vuelo (en vez de reutilizar uno global)
//     - clona los materiales del libro (no comparte el array con
//       el mesh original, así no le pisa opacidad/estado)
//     - ARRANCA el loop de animación con requestAnimationFrame(paso)
//       (esto es lo que faltaba y hacía que nunca pasara nada)
//     - al terminar el vuelo, destruye el renderer de vuelo antes
//       de abrir el lector 2D
// ------------------------------------------------------------------



function volarLibroYAbrir(rutaPdf, malla) {
  if (!malla) {
    abrirLector(rutaPdf, null);
    return;
  }

  crearRendererVuelo();

  const rect = obtenerRectPantalla(malla);
  if (!rect) {
    destruirRendererVuelo();
    abrirLector(rutaPdf, malla);
    return;
  }

  const DURACION = 1100;
  const ESCALA_FINAL = 1.65;

  const pxPorUnidad = rect.height / ALTO_LIBRO;
  const anchoMundo = window.innerWidth / pxPorUnidad;
  const altoMundo = window.innerHeight / pxPorUnidad;

  camaraVuelo.left = -anchoMundo / 2;
  camaraVuelo.right = anchoMundo / 2;
  camaraVuelo.top = altoMundo / 2;
  camaraVuelo.bottom = -altoMundo / 2;
  camaraVuelo.updateProjectionMatrix();

  rendererVuelo.setSize(window.innerWidth, window.innerHeight, false);

  const centroPxX = rect.left + rect.width / 2;
  const centroPxY = rect.top + rect.height / 2;
  const mundoX = (centroPxX - window.innerWidth / 2) / pxPorUnidad;
  const mundoY = -(centroPxY - window.innerHeight / 2) / pxPorUnidad;

  // Clon con materiales propios: así la animación de vuelo (opacidad,
  // transform) nunca contamina al libro original de la estantería.
  mallaVuelo = new THREE.Mesh(
    malla.geometry,
    malla.material.map((mat) => mat.clone())
  );
  mallaVuelo.position.set(mundoX, mundoY, 0);
  mallaVuelo.rotation.copy(malla.rotation);
  mallaVuelo.scale.set(1, 1, 1);
  escenaVuelo.add(mallaVuelo);

  malla.visible = false;
  vueloContenedor.classList.add('visible');

  const rotacionInicial = malla.rotation.y;
  const rotacionIntermedia = rotacionInicial + Math.PI * 0.18;
  const rotacionFinal = -Math.PI / 2;

  const t0 = performance.now();

  function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
  function easeInOutCubic(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  }

  function paso(ahora) {
    const t = Math.min(1, (ahora - t0) / DURACION);

    // FASE 1 (0%-35%): el libro sale del estante
    const tSalida = Math.min(t / 0.35, 1);
    const eSalida = easeOutCubic(tSalida);
    mallaVuelo.position.z = 0.15 * eSalida;
    mallaVuelo.rotation.y = rotacionInicial + (rotacionIntermedia - rotacionInicial) * eSalida;

    // FASE 2 (25%-100%): crece y viaja al centro
    const tCentro = Math.max(0, Math.min(1, (t - 0.25) / 0.75));
    const eCentro = easeInOutCubic(tCentro);
    mallaVuelo.position.x = mundoX + (0 - mundoX) * eCentro;
    mallaVuelo.position.y = mundoY + (0 - mundoY) * eCentro;
    mallaVuelo.scale.setScalar(1 + (ESCALA_FINAL - 1) * eCentro);

    // FASE 3 (35%-100%): termina de girar hacia la cámara
    const tGiro = Math.max(0, Math.min(1, (t - 0.35) / 0.65));
    const eGiro = easeInOutCubic(tGiro);
    mallaVuelo.rotation.y = rotacionIntermedia + (rotacionFinal - rotacionIntermedia) * eGiro;

    if (t < 1) {
      requestAnimationFrame(paso);
      return;
    }

    // ---- animación de vuelo terminada ----
    mallaVuelo.position.set(0, 0, 0.15);
    mallaVuelo.scale.setScalar(ESCALA_FINAL);
    mallaVuelo.rotation.y = rotacionFinal;

    // Tomamos el rectángulo EXACTO donde quedó el libro ya grande,
    // en pantalla, para que abrirLector() arranque justo ahí y la
    // apertura (portada girando + páginas del PDF) continúe sin salto.
    const rectFinal = proyectarCajaAPantalla(mallaVuelo, camaraVuelo, rendererVuelo);

    vueloContenedor.classList.remove('visible');
    destruirRendererVuelo(); // <- se destruye acá, no se deja vivo

    abrirLector(rutaPdf, malla, rectFinal);
  }

  // ESTA LÍNEA ES LA QUE FALTABA EN TU CÓDIGO ORIGINAL:
  requestAnimationFrame(paso);
}

async function abrirLector(rutaPdf, malla, rectYaGrande = null) {
  const pdfjsLib = await obtenerPdfjs();

  mallaOrigenLectura = malla;
  const libro = malla ? malla.userData.libro : null;
  const rect = rectYaGrande || (malla ? obtenerRectPantalla(malla) : null);

  lectorPdf.classList.add('visible');
  if (libro) construirTapaHTML(libro);

  // --- Estado inicial: tapa y libro en la misma posición/tamaño, sin transición ---
  portadaFlipEl.classList.remove('oculta');
  portadaFlipEl.style.transition = 'none';
  libroAbiertoEl.style.transition = 'none';

  if (rect) {
    for (const el of [portadaFlipEl, libroAbiertoEl]) {
      el.style.top = `${rect.top}px`;
      el.style.left = `${rect.left}px`;
      el.style.width = `${rect.width}px`;
      el.style.height = `${rect.height}px`;
    }
  }
  portadaFlipEl.style.transform = 'rotateY(0deg)';

  portadaFlipEl.offsetHeight;
  libroAbiertoEl.offsetHeight;

  portadaFlipEl.style.transition = '';
  libroAbiertoEl.style.transition = '';

  if (malla) malla.visible = false;

  modoUnaPagina = esMovilLector();
  actualizarModoVista();

  try {
    pdfActual = await pdfjsLib.getDocument(rutaPdf).promise;
    totalPaginasPdf = pdfActual.numPages;
    rutaPdfActual = rutaPdf;

    paginaIzqActual = 1;
    try {
      const guardada = Number(localStorage.getItem(claveUltimaPagina(rutaPdf)));
      if (guardada >= 1 && guardada <= totalPaginasPdf) {
        paginaIzqActual = guardada;
      }
    } catch (e) {
      // localStorage puede fallar (modo privado, cuota llena, etc.) —
      // no es grave, simplemente arranca en la página 1.
    }

    // PASO 1: la tapa empieza a girar
    await esperar(30);
    portadaFlipEl.style.transform = 'rotateY(-100deg)';
    await esperar(500);

        // PASO 2: calculamos el tamaño FINAL real (según aspecto del PDF y
    // el tamaño ACTUAL de la ventana) y se lo aplicamos tanto al libro
    // real como a la tapa (misma posición en pantalla, para que el
    // resto del giro se vea proporcional).
    const paginaRef = await pdfActual.getPage(1);
    const vpRef = paginaRef.getViewport({ scale: 1 });
    const aspectoPagina = vpRef.width / vpRef.height;

    // Reutilizamos la misma función que ya usa recalcularTamanoYRenderizar(),
    // así el tamaño inicial y los resizes posteriores son consistentes y
    // ambos se adaptan al innerWidth/innerHeight actuales.
    const { anchoDestino: anchoFinalInterior, altoDestino } = calcularTamanoDestino(aspectoPagina);
    const anchoCubierta = modoUnaPagina ? anchoFinalInterior : anchoFinalInterior / 2;

    const leftFinal = (window.innerWidth - anchoFinalInterior) / 2;
    const topFinal = (window.innerHeight - altoDestino) / 2;

    libroAbiertoEl.style.left = `${leftFinal}px`;
    libroAbiertoEl.style.width = `${anchoFinalInterior}px`;
    libroAbiertoEl.style.top = `${topFinal}px`;
    libroAbiertoEl.style.height = `${altoDestino}px`;

    portadaFlipEl.style.left = `${leftFinal}px`;
    portadaFlipEl.style.width = `${anchoCubierta}px`;
    portadaFlipEl.style.top = `${topFinal}px`;
    portadaFlipEl.style.height = `${altoDestino}px`;

    await esperar(750);

    // PASO 3: layout ya asentado y correcto -> recién ahora renderizamos
    await renderizarPaginasActuales();

    // PASO 4: la tapa termina de girar y se desvanece, revelando el libro real
    portadaFlipEl.style.transform = 'rotateY(-180deg)';
    await esperar(500);
    portadaFlipEl.classList.add('oculta');
  } catch (err) {
    console.error('No se pudo cargar el PDF', err);
    cerrarLector();
  }
}

function solicitarPantallaCompleta() {
  // Si ya está en fullscreen (lo activó a mano antes), no hacemos nada
  // y devolvemos una promesa ya resuelta.
  if (document.fullscreenElement) return Promise.resolve();

  const el = document.documentElement;
  const solicitar =
    el.requestFullscreen ||
    el.webkitRequestFullscreen ||
    el.mozRequestFullScreen ||
    el.msRequestFullscreen;

  if (!solicitar) return Promise.resolve();

  return solicitar.call(el).catch(() => {
    // el navegador lo bloqueó o no lo soporta acá; seguimos igual
  });
}
 
function salirDePantallaCompleta() {
  const salir =
    document.exitFullscreen ||
    document.webkitExitFullscreen ||
    document.mozCancelFullScreen ||
    document.msExitFullscreen;
 
  if (document.fullscreenElement && salir) {
    salir.call(document).catch(() => {});
  }
}
 
 


})();
