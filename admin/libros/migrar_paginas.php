<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../layout/admin/login.php';
require_once __DIR__ . '/../../layout/admin/datos_usuario.php';
require_once __DIR__ . '/../../layout/admin/comprueba_admin.php';
require_once __DIR__ . '/../../layout/admin/parte1.php';
?>
<main class="app-main"><div class="container-fluid py-4">
  <h3>Migrar páginas de libros</h3>
  <p>Convierte los PDF en páginas JPEG para que el lector solicite solo las páginas visibles. El PDF original se conserva durante la conversión.</p>
  <div class="card"><div class="card-body">
    <div id="resumen" class="mb-3">Calculando estado…</div>
    <div class="progress mb-3" style="height:22px"><div id="barra" class="progress-bar" style="width:0%">0%</div></div>
    <p id="estado" role="status">Listo para consultar.</p>
    <button id="procesar" class="btn btn-primary">Procesar libros pendientes</button>
    <div class="form-check mt-3">
      <input class="form-check-input" type="checkbox" id="confirmarBorrado">
      <label class="form-check-label" for="confirmarBorrado">Confirmo que quiero borrar los PDF originales de los libros ya procesados. Se perderá la descarga del PDF; la lectura optimizada seguirá disponible.</label>
    </div>
    <button id="borrar" class="btn btn-outline-danger mt-2" disabled>Borrar originales procesados</button>
  </div></div>
</div></main>
<script>
(() => {
 const api = 'migracion_paginas_api.php';
 const resumen = document.getElementById('resumen');
 const estado = document.getElementById('estado');
 const barra = document.getElementById('barra');
 const btnProcesar = document.getElementById('procesar');
 const btnBorrar = document.getElementById('borrar');
 const check = document.getElementById('confirmarBorrado');
 let datos = { total: 0, pendientes: 0, procesados: 0, bytes_liberables: 0 };
 const formato = n => {
   const u = ['B','KB','MB','GB','TB']; let i=0; n=Number(n)||0;
   while(n>=1024 && i<u.length-1){n/=1024;i++;}
   return `${n.toFixed(i ? 2 : 0)} ${u[i]}`;
 };
 async function refrescar() {
   const r = await fetch(`${api}?accion=estado`, { credentials: 'same-origin' });
   datos = await r.json();
   const neto = Number(datos.ahorro_neto_estimado) || 0;
   const balance = neto >= 0 ? `Ahorro neto estimado: ${formato(neto)}` : `Aumento neto estimado: ${formato(-neto)}`;
   resumen.textContent = `PDF con original: ${datos.total} · Procesados: ${datos.procesados} · Pendientes: ${datos.pendientes} · Originales: ${formato(datos.bytes_liberables)} · Páginas generadas: ${formato(datos.bytes_paginas)} · ${balance}`;
   btnBorrar.disabled = !check.checked || datos.procesados === 0;
 }
 async function ejecutar(accion) {
   btnProcesar.disabled = btnBorrar.disabled = true;
   let hechos = 0; let bytesBorrados = 0;
   const totalInicial = accion === 'procesar' ? datos.pendientes : datos.procesados;
   estado.textContent = accion === 'procesar' ? 'Convirtiendo libros, uno por uno…' : 'Borrando originales procesados…';
   try {
     while (true) {
       const r = await fetch(api, { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({accion}) });
       const d = await r.json(); if (!r.ok || d.error) throw new Error(d.error || `HTTP ${r.status}`);
       if (d.fin) break;
       hechos++;
       if (accion === 'borrar') bytesBorrados += Number(d.bytes_liberados) || 0;
       const pct = totalInicial ? Math.min(100, Math.round(hechos / totalInicial * 100)) : 100;
       barra.style.width = `${pct}%`; barra.textContent = `${pct}%`;
       estado.textContent = accion === 'procesar' ? `Procesado: ${d.titulo} (${d.pages} páginas)` : `Borrado: ${d.titulo} · liberados ${formato(bytesBorrados)} en total`;
       await refrescar();
     }
     estado.textContent = hechos ? (accion === 'borrar' ? `Terminado. PDF eliminados: ${hechos}. Espacio liberado: ${formato(bytesBorrados)}.` : `Terminado. Libros convertidos: ${hechos}.`) : 'No hay libros pendientes para esta acción.';
     await refrescar();
   } catch(e) { estado.textContent = `Se detuvo: ${e.message}. Puedes volver a iniciarlo; continuará con los pendientes.`; }
   finally { btnProcesar.disabled = false; btnBorrar.disabled = !check.checked || datos.procesados === 0; }
 }
 check.addEventListener('change', () => { btnBorrar.disabled = !check.checked || datos.procesados === 0; });
 btnProcesar.addEventListener('click', () => ejecutar('procesar'));
 btnBorrar.addEventListener('click', () => { if(check.checked && confirm('Se eliminarán los PDF originales de todos los libros ya procesados. ¿Continuar?')) ejecutar('borrar'); });
 refrescar().catch(e => { resumen.textContent = `No se pudo consultar el estado: ${e.message}`; });
})();
</script>
<?php require_once __DIR__ . '/../../layout/admin/parte2.php'; ?>
