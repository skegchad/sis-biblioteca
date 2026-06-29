<?php

include ('../../app/config/config.php');
include ('../../app/config/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Conoce a BMO</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Nunito:wght@400;700;900&display=swap');

    :root {
      --verde-oscuro:   #1a3d2b;
      --verde-medio:    #2e7d52;
      --verde-claro:    #4caf7d;
      --verde-neon:     #72f5a8;
      --verde-pantalla: #a8ffcc;
      --crema:          #f0fdf4;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* Cruz del sistema solo sobre el canvas de BMO */
    #canvas { cursor: move; }

    body {
      background-color: var(--verde-oscuro);
      font-family: 'Nunito', sans-serif;
      color: var(--crema);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        radial-gradient(circle at 20% 20%, rgba(72,245,168,0.07) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(46,125,82,0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }

    header {
      width: 100%;
      padding: 2.5rem 2rem 1rem;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    header h1 {
      font-family: 'Press Start 2P', monospace;
      font-size: clamp(1.2rem, 4vw, 2.2rem);
      color: var(--verde-neon);
      text-shadow:
        0 0 8px var(--verde-neon),
        0 0 24px rgba(114,245,168,0.5);
      letter-spacing: 2px;
      line-height: 1.6;
    }

    header .subtitle {
      margin-top: 0.75rem;
      font-size: 0.95rem;
      color: var(--verde-claro);
      font-weight: 700;
      letter-spacing: 1px;
    }

    .divider {
      width: 80%;
      max-width: 700px;
      height: 4px;
      margin: 1.5rem auto;
      background: repeating-linear-gradient(
        90deg,
        var(--verde-neon) 0px, var(--verde-neon) 12px,
        transparent 12px, transparent 20px
      );
      opacity: 0.6;
    }

    main {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 3rem;
      padding: 1rem 2rem 4rem;
      max-width: 1100px;
      width: 100%;
      position: relative;
      z-index: 1;
    }

    /* Panel de texto */
    .info-panel {
      flex: 1 1 280px;
      max-width: 380px;
    }

    .info-panel h2 {
      font-family: 'Press Start 2P', monospace;
      font-size: 1rem;
      color: var(--verde-neon);
      margin-bottom: 1rem;
      line-height: 1.7;
    }

    .info-panel p {
      font-size: 1.05rem;
      line-height: 1.85;
      color: var(--crema);
      font-weight: 400;
    }

    .info-panel p strong { color: var(--verde-neon); font-weight: 900; }

    .badge {
      display: inline-block;
      margin-top: 1.4rem;
      padding: 0.45rem 1rem;
      background: rgba(114,245,168,0.12);
      border: 2px solid var(--verde-neon);
      border-radius: 4px;
      font-family: 'Press Start 2P', monospace;
      font-size: 0.55rem;
      color: var(--verde-neon);
      letter-spacing: 1px;
    }

    /* ── Visor 3D flotante ── */
    .viewer-wrap {
      flex: 1 1 240px;
      max-width: 320px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
    }

    /* Sin borde, sin fondo — solo el canvas transparente flotando */
    .viewer-frame {
      width: 100%;
      aspect-ratio: 1 / 1;
      background: transparent;
      border: none;
      box-shadow: none;
      position: relative;
      overflow: visible;
    }

    /* Sombra proyectada debajo del modelo para sensación de flotación */
    .viewer-frame::after {
      content: '';
      position: absolute;
      bottom: -18px;
      left: 50%;
      transform: translateX(-50%);
      width: 55%;
      height: 28px;
      background: radial-gradient(ellipse, rgba(114,245,168,0.25) 0%, transparent 75%);
      border-radius: 50%;
      pointer-events: none;
      animation: shadowPulse 3s ease-in-out infinite;
    }

    @keyframes shadowPulse {
      0%, 100% { opacity: 0.5; transform: translateX(-50%) scaleX(1); }
      50%       { opacity: 0.9; transform: translateX(-50%) scaleX(1.1); }
    }

    canvas {
      width: 100% !important;
      height: 100% !important;
      display: block;
      background: transparent;
    }

    .hint {
      font-size: 0.78rem;
      color: var(--verde-claro);
      font-weight: 700;
      letter-spacing: 0.5px;
      opacity: 0.8;
    }

    .error-msg {
      position: absolute;
      inset: 0;
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      font-family: 'Press Start 2P', monospace;
      font-size: 0.6rem;
      color: var(--verde-neon);
      text-align: center;
      padding: 1rem;
    }
    .error-msg.visible { display: flex; }

    footer {
      padding: 1.5rem;
      font-size: 0.75rem;
      color: var(--verde-medio);
      text-align: center;
      position: relative;
      z-index: 1;
    }
  </style>
</head>
<body>

  <header>
    <h1>⬛ CONOCE A BMO ⬛</h1>
    <p class="subtitle">Tu pequeño amigo de Hora de Aventura</p>
  </header>

  <div class="divider"></div>

  <main>
    <div class="info-panel">
      <h2>¡Hola,<br/>soy BMO!</h2>
      <p>
        <strong>BMO</strong> es una pequeña consola de videojuegos con vida propia.
        Vive con <strong>Finn y Jake</strong> en la Casa del Árbol y es uno de sus
        mejores amigos.
      </p>
      <p style="margin-top:1rem;">
        BMO puede jugar videojuegos, tomar fotos, reproducir música y hasta
        actuar solo en sus propias aventuras imaginarias. ¡Nadie hace "BMO" como BMO!
      </p>
      <span class="badge">▶ MODELO INTERACTIVO 3D</span>
    </div>

    <div class="viewer-wrap">
      <div class="viewer-frame" id="viewer-frame">
        <canvas id="canvas"></canvas>
        <div class="error-msg" id="error-msg">
          <span>⚠</span>
          <span>No se encontró<br/>bmo.glb</span>
          <span style="opacity:.6">Coloca el archivo<br/>en la misma carpeta</span>
        </div>
      </div>
      <p class="hint">✦ Click y arrastra para rotar a BMO</p>
    </div>
  </main>

  <footer>BMO © Hora de Aventura · Cartoon Network · Diseño fan-made</footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script>
  (function () {

    // ── Cargar GLTFLoader ───────────────────────────────────────────────────
    const loaderScript = document.createElement('script');
    loaderScript.src = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js';
    loaderScript.onload = initScene;
    loaderScript.onerror = () => {
      const s2 = document.createElement('script');
      s2.src = 'https://unpkg.com/three@0.128.0/examples/js/loaders/GLTFLoader.js';
      s2.onload = initScene;
      s2.onerror = showError;
      document.head.appendChild(s2);
    };
    document.head.appendChild(loaderScript);

    function showError() {
      document.getElementById('error-msg').classList.add('visible');
      document.getElementById('canvas').style.display = 'none';
    }

    function initScene() {
      const canvas = document.getElementById('canvas');
      const frame  = document.getElementById('viewer-frame');

      // Renderer con fondo transparente
      const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
      renderer.setClearColor(0x000000, 0); // totalmente transparente
      renderer.setPixelRatio(window.devicePixelRatio);
      renderer.outputEncoding = THREE.sRGBEncoding;

      function resize() {
        const w = frame.clientWidth, h = frame.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
      }

      const scene = new THREE.Scene();
      // Sin color de fondo en la escena
      scene.background = null;

      const camera = new THREE.PerspectiveCamera(45, 1, 0.01, 100);
      camera.position.set(0, 0.3, 3);

      // Luces
      const ambient = new THREE.AmbientLight(0xa8ffcc, 0.8);
      scene.add(ambient);

      const dirLight = new THREE.DirectionalLight(0xffffff, 1.4);
      dirLight.position.set(3, 6, 4);
      scene.add(dirLight);

      const rimLight = new THREE.DirectionalLight(0x72f5a8, 0.6);
      rimLight.position.set(-4, 2, -3);
      scene.add(rimLight);

      // Luz de relleno suave desde abajo (color verde para el efecto flotante)
      const fillLight = new THREE.PointLight(0x4caf7d, 0.4, 10);
      fillLight.position.set(0, -2, 1);
      scene.add(fillLight);

      // Cargar modelo
      const loader = new THREE.GLTFLoader();
      let model = null;
      let baseY = 0;
      let floatT = 0;

      loader.load(
        'bmo.glb',
        (gltf) => {
          model = gltf.scene;

          const box    = new THREE.Box3().setFromObject(model);
          const center = box.getCenter(new THREE.Vector3());
          const size   = box.getSize(new THREE.Vector3());
          const maxDim = Math.max(size.x, size.y, size.z);
          const scale  = 1.5 / maxDim;

          model.scale.setScalar(scale);
          model.position.sub(center.multiplyScalar(scale));
          baseY = model.position.y;

          scene.add(model);
          camera.position.set(0, 0, 2.8);
        },
        undefined,
        (err) => { console.warn('Error:', err); showError(); }
      );

      // ── Rotación con mouse ──────────────────────────────────────────────
      let isDragging = false;
      let prevX = 0, prevY = 0;
      let rotX = 0, rotY = 0;
      let velX = 0, velY = 0;

      canvas.addEventListener('mousedown', (e) => {
        isDragging = true;
        document.body.classList.add('dragging');
        prevX = e.clientX; prevY = e.clientY;
        velX = 0; velY = 0;
      });

      window.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.classList.remove('dragging');
      });

      window.addEventListener('mousemove', (e) => {
        if (!isDragging || !model) return;
        const dx = e.clientX - prevX;
        const dy = e.clientY - prevY;
        velX = dy * 0.008;  // vertical   → inclina sobre X
        velY = dx * 0.008;  // horizontal → da la vuelta sobre Y
        rotX += velX;
        rotY += velY;
        prevX = e.clientX; prevY = e.clientY;
      });

      // Touch
      canvas.addEventListener('touchstart', (e) => {
        isDragging = true;
        prevX = e.touches[0].clientX;
        prevY = e.touches[0].clientY;
      }, { passive: true });

      canvas.addEventListener('touchend', () => { isDragging = false; });

      canvas.addEventListener('touchmove', (e) => {
        if (!isDragging || !model) return;
        const dx = e.touches[0].clientX - prevX;
        const dy = e.touches[0].clientY - prevY;
        velX = dy * 0.008;
        velY = dx * 0.008;
        rotX += velX;
        rotY += velY;
        prevX = e.touches[0].clientX;
        prevY = e.touches[0].clientY;
      }, { passive: true });

      // ── Loop ────────────────────────────────────────────────────────────
      function animate() {
        requestAnimationFrame(animate);
        resize();

        if (model) {
          // Flotación suave
          floatT += 0.018;
          model.position.y = baseY + Math.sin(floatT) * 0.08;

          if (!isDragging) {
            velX *= 0.92; velY *= 0.92;
            rotX += velX; rotY += velY;
            if (Math.abs(velY) < 0.001) rotY += 0.004;
          }
          model.rotation.x = rotX;
          model.rotation.y = rotY;
        }

        renderer.render(scene, camera);
      }

      animate();
    }
  })();
  </script>
</body>
</html>