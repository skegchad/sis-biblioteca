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

  // Libros "caídos": tres de pie, dos que se van inclinando como fichas de dominó
  // y uno acostado en el suelo sobre el que se apoyan.
  const librosCaidos = (escala = 1) => svg(116, 100, `
    <rect x="72" y="86" width="42" height="14" rx="1.5" fill="#48613d"/>
    <rect x="104" y="86" width="10" height="14" fill="#f2ead6"/>
    <rect x="78" y="86" width="2.5" height="14" fill="#d9b66a"/>
    <rect x="4" y="22" width="13" height="78" rx="1.5" fill="#7a3b2e"/>
    <rect x="4" y="30" width="13" height="3" fill="#d9b66a"/>
    <rect x="18" y="28" width="12" height="72" rx="1.5" fill="#2f5d62"/>
    <rect x="18" y="36" width="12" height="3" fill="#d9b66a"/>
    <g transform="rotate(8 45 100)">
      <rect x="31" y="24" width="14" height="76" rx="1.5" fill="#c9a24a"/>
      <rect x="31" y="32" width="14" height="3" fill="#7a3b2e"/>
    </g>
    <g transform="rotate(20 59 100)">
      <rect x="46" y="30" width="13" height="70" rx="1.5" fill="#4e4772"/>
      <rect x="46" y="38" width="13" height="3" fill="#d9b66a"/>
    </g>
    <g transform="rotate(38 72 100)">
      <rect x="60" y="36" width="12" height="64" rx="1.5" fill="#873f55"/>
      <rect x="60" y="44" width="12" height="3" fill="#d9b66a"/>
    </g>`, escala);

  const jarronDorado = (escala = 1) => svg(40, 80, `
    <path d="M20 34C18 21 14 13 10 5M20 34C20 21 21 12 22 3M20 34C23 22 28 15 33 9" stroke="#8a6a3a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
    <ellipse cx="10" cy="6" rx="2" ry="4.5" fill="#b98f4d" transform="rotate(-28 10 6)"/>
    <ellipse cx="22" cy="4" rx="2" ry="4.5" fill="#b98f4d"/>
    <ellipse cx="33" cy="10" rx="2" ry="4.5" fill="#b98f4d" transform="rotate(32 33 10)"/>
    <path d="M14 79h12c4-8 8-16 6-28-1-8-6-10-6-17H14c0 7-5 9-6 17-2 12 2 20 6 28z" fill="#d4a73c"/>
    <path d="M14 79c-4-8-8-16-6-28 1-8 6-10 6-17h4c0 7-4 9-5 17-2 12 1 20 5 28z" fill="#f0cf6e"/>
    <path d="M26 79c4-8 8-16 6-28-1-8-6-10-6-17h-3c0 7 4 9 5 17 2 12-1 20-5 28z" fill="#a87b22" opacity=".7"/>
    <rect x="13" y="31" width="14" height="4" rx="1.5" fill="#e6c25e"/>`, escala);

  const pina = (escala = 1) => svg(44, 90, `
    <defs><clipPath id="deco-pina"><ellipse cx="22" cy="63" rx="16" ry="26.5"/></clipPath></defs>
    <path d="M22 42C14 30 12 16 16 3c4 9 6 20 6 39z" fill="#3c7f43"/>
    <path d="M22 42C30 30 32 16 28 3c-4 9-6 20-6 39z" fill="#3c7f43"/>
    <path d="M22 42C18 26 20 10 22 0c2 10 4 26 0 42z" fill="#4c9a54"/>
    <path d="M22 44C10 38 5 30 6 19c8 7 14 16 16 25z" fill="#2f6a3a"/>
    <path d="M22 44C34 38 39 30 38 19c-8 7-14 16-16 25z" fill="#2f6a3a"/>
    <ellipse cx="22" cy="63" rx="16" ry="26.5" fill="#e9c640"/>
    <g clip-path="url(#deco-pina)" stroke="#c4961a" stroke-width="1.3" opacity=".85">
      <path d="M-6 40L50 96M-6 52L50 108M-6 28L50 84M-6 16L50 72M-6 4L50 60"/>
      <path d="M50 40L-6 96M50 52L-6 108M50 28L-6 84M50 16L-6 72M50 4L-6 60"/>
    </g>
    <ellipse cx="15" cy="58" rx="3" ry="11" fill="#fff" opacity=".18"/>`, escala);

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
    () => maceta(1.35),        // 1.º  → maceta
    () => cactus(1.3),         // 3.º  → cactus
    () => librosCaidos(0.9),   // 5.º  → libros caídos de lado
    () => jarronDorado(1.3),   // 7.º  → jarrón dorado
    () => pina(1.15),          // 9.º  → piña
    () => suculenta(1.0),      // 11.º → suculenta
    () => marco(1.1),          // 13.º → marco con foto
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