/* ============================================================
   DECORACIÓN DE LOS ESTANTES
   Script normal (NO módulo). Cargar en el PHP, después de que exista
   <main id="estantes-categorias">.

   Regla: los estantes IMPARES (1.º, 3.º, 5.º...) llevan UN solo adorno,
   en un compartimento aparte, alternando de lado:
       1.º → maceta   (derecha)
       3.º → cactus   (izquierda)
       5.º → libros caídos (derecha)
       7.º → jarrón dorado (izquierda) ... y así en ciclo.
   Para cambiar el orden o los adornos, edita la lista DECORACIONES.

   Observa el contenedor: cada vez que estanteria-3d.js crea los
   estantes, agrega la columna. No toca nada del resto de la lógica.
   ============================================================ */
(function () {
  'use strict';

  const contenedor = document.getElementById('estantes-categorias');
  if (!contenedor) return;

  const svg = (ancho, alto, cuerpo, escala = 1) =>
    `<svg class="deco-item" viewBox="0 0 ${ancho} ${alto}" style="--h:${escala}" aria-hidden="true" focusable="false">${cuerpo}</svg>`;

  // ---------- Adornos (SVG simples, sin imágenes externas) ----------

  const maceta = (escala = 1) => svg(60, 100, `
    <path d="M30 64C12 58 6 38 14 16c14 10 20 30 16 48z" fill="#3f7d46"/>
    <path d="M30 64C48 56 56 38 48 18c-14 10-20 28-18 46z" fill="#2f6a3a"/>
    <path d="M30 64C22 46 22 26 30 6c8 20 8 40 0 58z" fill="#4a9552"/>
    <path d="M30 64C16 56 14 46 6 40c10-2 20 6 24 24z" fill="#35703c"/>
    <path d="M30 64C44 56 46 46 54 40c-10-2-20 6-24 24z" fill="#3a7a42"/>
    <path d="M30 62C28 44 28 26 30 12" stroke="#8fd096" stroke-width="1" fill="none" opacity=".55"/>
    <path d="M16 66h28l-4 34H20z" fill="#b4623a"/>
    <rect x="13" y="62" width="34" height="8" rx="2" fill="#cc7746"/>
    <path d="M18 76h24" stroke="#8f4a28" stroke-width="1" opacity=".5"/>`, escala);

  const cactus = (escala = 1) => svg(44, 72, `
    <path d="M16 34H10V21" stroke="#4f9a5a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    <path d="M28 28h6V17" stroke="#4f9a5a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    <rect x="16" y="8" width="12" height="40" rx="6" fill="#58a663"/>
    <path d="M22 12v34" stroke="#3f8049" stroke-width="1"/>
    <path d="M9 48h26l-3 22H12z" fill="#efe7d6"/>
    <rect x="7" y="44" width="30" height="7" rx="2" fill="#fbf6ea"/>`, escala);

  // Libros "caídos": tres libros con el MISMO alto que los libros del catálogo
  // (el svg ocupa todo el alto libre del estante: escala 3 = alto del lienzo).
  // Dos de pie y uno inclinado hacia la izquierda: su esquina de arriba se arrima al libro azul (se dibuja detrás de él).
  const librosCaidos = (escala = 3) => svg(204, 380, `
    <g transform="rotate(-6 196.5 380)">
      <rect x="146.5" y="0" width="50" height="380" rx="4" fill="#873f55"/>
      <rect x="146.5" y="44" width="50" height="10" fill="#d9b66a"/>
      <rect x="146.5" y="64" width="50" height="3" fill="#d9b66a" opacity=".7"/>
      <rect x="146.5" y="326" width="50" height="8" fill="#d9b66a"/>
      <rect x="151.5" y="130" width="5" height="130" rx="2.5" fill="#fff" opacity=".16"/>
    </g>
    <rect x="56" y="0" width="52" height="380" rx="4" fill="#2f5d62"/>
    <rect x="56" y="40" width="52" height="10" fill="#d9b66a"/>
    <rect x="56" y="60" width="52" height="3" fill="#d9b66a" opacity=".7"/>
    <rect x="56" y="326" width="52" height="8" fill="#d9b66a"/>
    <rect x="61" y="120" width="5" height="150" rx="2.5" fill="#fff" opacity=".16"/>
    <rect x="0" y="0" width="56" height="380" rx="4" fill="#7a3b2e"/>
    <rect x="0" y="48" width="56" height="10" fill="#d9b66a"/>
    <rect x="0" y="68" width="56" height="3" fill="#d9b66a" opacity=".7"/>
    <rect x="0" y="326" width="56" height="8" fill="#d9b66a"/>
    <rect x="5" y="140" width="5" height="160" rx="2.5" fill="#fff" opacity=".16"/>
    <rect x="0" y="372" width="108" height="8" fill="#000" opacity=".25"/>`, escala);

  const jarronDorado = (escala = 1) => svg(40, 80, `
    <path d="M20 34C18 21 14 13 10 5M20 34C20 21 21 12 22 3M20 34C23 22 28 15 33 9" stroke="#8a6a3a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
    <ellipse cx="10" cy="6" rx="2" ry="4.5" fill="#b98f4d" transform="rotate(-28 10 6)"/>
    <ellipse cx="22" cy="4" rx="2" ry="4.5" fill="#b98f4d"/>
    <ellipse cx="33" cy="10" rx="2" ry="4.5" fill="#b98f4d" transform="rotate(32 33 10)"/>
    <path d="M14 79h12c4-8 8-16 6-28-1-8-6-10-6-17H14c0 7-5 9-6 17-2 12 2 20 6 28z" fill="#d4a73c"/>
    <path d="M14 79c-4-8-8-16-6-28 1-8 6-10 6-17h4c0 7-4 9-5 17-2 12 1 20 5 28z" fill="#f0cf6e"/>
    <path d="M26 79c4-8 8-16 6-28-1-8-6-10-6-17h-3c0 7 4 9 5 17 2 12-1 20-5 28z" fill="#a87b22" opacity=".7"/>
    <rect x="13" y="31" width="14" height="4" rx="1.5" fill="#e6c25e"/>`, escala);

  // Lirio araña rojo (Lycoris radiata): tallos verdes desnudos y gruesos, cada uno
  // con una umbela de pétalos ondulados y rizados, y estambres larguísimos con
  // la punta dorada que sobresalen en abanico.
  const lirioArana = (escala = 1) => {
    const f = (n) => Math.round(n * 10) / 10;
    // pétalo curvado (apunta hacia arriba y se riza hacia la derecha)
    const P = 'M0 0C-5 -7 -8 -14 -3 -21C0 -25 -1 -29 4 -31C6 -26 9 -23 7 -17C6 -11 3 -5 0 0Z';
    const petalos = [[-140, .8], [-116, .95], [-92, 1.05], [-68, .9], [-44, 1.05], [-20, .95], [4, 1.1], [26, .9], [48, 1.05], [70, .95], [92, 1.05], [116, .92], [140, .8]];
    const estambres = [[-118, .8], [-102, 1], [-86, .74], [-70, .96], [-54, .78], [-38, 1], [-22, .72], [-8, .94], [8, .8], [22, 1], [38, .74], [54, .96], [70, .78], [86, 1], [102, .76], [118, .9]];

    const flor = (x, y, s, giro) => {
      let g = '';
      // estambres (detrás de los pétalos)
      estambres.forEach(([a, r]) => {
        const sg = a < 0 ? -1 : 1;
        const ex = f(34 * r), ey = f(-52 * r);
        g += `<g transform="scale(${sg} 1) rotate(${Math.abs(a)})">` +
          `<path d="M0 0C${f(2 * r)} ${f(-20 * r)} ${f(12 * r)} ${f(-42 * r)} ${ex} ${ey}" stroke="#e4432c" stroke-width="1" stroke-linecap="round" fill="none"/>` +
          `<ellipse cx="${ex}" cy="${ey}" rx="1.9" ry="2.6" transform="rotate(55 ${ex} ${ey})" fill="#f6bb3a"/></g>`;
      });
      // pétalos: capa oscura + capa clara + vena
      petalos.forEach(([a, sc]) => {
        const sg = a < 0 ? -1 : 1;
        g += `<g transform="scale(${sg} 1) rotate(${Math.abs(a)}) scale(${f(1.22 * sc)})">` +
          `<path d="${P}" fill="#a8101a"/>` +
          `<path d="${P}" transform="translate(.6 -1.2) scale(.8)" fill="#ec3828"/>` +
          `<path d="M0 -1C-2 -9 -2 -19 4 -29" stroke="#ff9877" stroke-width=".8" fill="none" opacity=".85"/></g>`;
      });
      return `<g transform="translate(${x} ${y}) rotate(${giro}) scale(${s})">${g}<circle r="2.6" fill="#8d0d16"/></g>`;
    };

    return svg(150, 250, `
      <path d="M75 218C70 186 50 146 38 114" stroke="#5f6c25" stroke-width="3.4" fill="none" stroke-linecap="round"/>
      <path d="M75 218C77 160 74 108 76 66" stroke="#6d7b2c" stroke-width="3.8" fill="none" stroke-linecap="round"/>
      <path d="M75 218C82 188 100 144 112 104" stroke="#5f6c25" stroke-width="3.4" fill="none" stroke-linecap="round"/>
      ${flor(38, 112, 0.72, -10)}
      ${flor(112, 102, 0.7, 12)}
      ${flor(76, 62, 1, 0)}
      <path d="M57 250h36c6-10 9-22 7-34-1-7-5-10-7-14H57c-2 4-6 7-7 14-2 12 1 24 7 34z" fill="#1f3a44"/>
      <path d="M57 250c-6-10-9-22-7-34 1-7 5-10 7-14h5c-2 4-6 7-7 14-2 12 1 24 7 34z" fill="#2f5a68"/>
      <rect x="55" y="196" width="40" height="8" rx="2.5" fill="#2a4b58"/>
      <path d="M52 228h46" stroke="#d9b66a" stroke-width="2.2" opacity=".85"/>`, escala);
  };

  const suculenta = (escala = 1) => {
    const petalos = [-64, -32, 0, 32, 64].map((a, i) =>
      `<ellipse cx="22" cy="21" rx="5" ry="10" transform="rotate(${a} 22 31)" fill="${i % 2 ? '#8bcba0' : '#6fb487'}"/>`
    ).join('');
    return svg(44, 52, `
      ${petalos}
      <ellipse cx="22" cy="27" rx="4" ry="6" fill="#a6dfb7"/>
      <path d="M10 35h24l-3 16H13z" fill="#d9d2c0"/>
      <rect x="8" y="32" width="28" height="6" rx="2" fill="#ece6d6"/>`, escala);
  };

  const marco = (escala = 1) => svg(52, 58, `
    <rect x="1" y="1" width="50" height="50" rx="2" fill="#3b2314"/>
    <rect x="5" y="5" width="42" height="42" fill="#e9a65b"/>
    <rect x="5" y="5" width="42" height="22" fill="#f2c27c"/>
    <circle cx="33" cy="25" r="7" fill="#fbe7b0"/>
    <path d="M5 47V35l10-8 8 7 10-10 14 11v12z" fill="#4b3a2a"/>
    <path d="M19 51l7 7 7-7z" fill="#2c1a0e"/>`, escala);

  // ---------- Qué adorno lleva cada estante impar (en ciclo) ----------
  // El lado se alterna solo: 1.º derecha, 3.º izquierda, 5.º derecha...
  // El número es el tamaño relativo (1 = ~un tercio del alto del estante).
  const DECORACIONES = [
    () => maceta(2.1),        // 1.º  → maceta
    () => cactus(2.2),         // 3.º  → cactus
    () => librosCaidos(3),   // 5.º  → libros caídos de lado
    () => jarronDorado(2.3),   // 7.º  → jarrón dorado
    () => lirioArana(2.6),     // 9.º  → jarrón con lirios araña rojos
    () => suculenta(1.6),      // 11.º → suculenta
    () => marco(1.55),          // 13.º → marco con foto
  ];

  function crearDecoracion(adorno) {
    const columna = document.createElement('div');
    columna.className = 'estante-decoracion';
    columna.setAttribute('aria-hidden', 'true');
    columna.innerHTML = `<div class="deco-celda">${DECORACIONES[adorno]()}</div>`;
    return columna;
  }

  function decorar() {
    // el estante especial de "resultados" nunca lleva decoración
    const secciones = [...contenedor.querySelectorAll(':scope > .estante-categoria')]
      .filter((s) => s.dataset.categoriaId !== 'resultados');

    secciones.forEach((seccion, indice) => {
      const actual = seccion.querySelector(':scope > .estante-decoracion');
      const esImpar = indice % 2 === 0; // índice 0 = 1.er estante

      if (!esImpar) {
        if (actual) actual.remove();
        seccion.classList.remove('con-decoracion', 'deco-izq', 'deco-der');
        return;
      }

      const k = indice / 2;                         // 0, 1, 2, ...
      const adorno = k % DECORACIONES.length;
      const lado = k % 2 === 0 ? 'der' : 'izq';
      const clave = `${adorno}-${lado}`;

      seccion.classList.add('con-decoracion');
      seccion.classList.toggle('deco-der', lado === 'der');
      seccion.classList.toggle('deco-izq', lado === 'izq');

      if (actual && actual.dataset.clave === clave) return;
      if (actual) actual.remove();

      const columna = crearDecoracion(adorno);
      columna.dataset.clave = clave;
      seccion.appendChild(columna);
    });
  }

  new MutationObserver(decorar).observe(contenedor, { childList: true });
  decorar();
})();