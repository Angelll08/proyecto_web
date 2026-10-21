// Archivo corregido y optimizado: foto.js

const video       = document.getElementById('video');
const btnCamara   = document.getElementById('btn-camara');
const btnTomar    = document.getElementById('btn-tomar');
const archivo     = document.getElementById('archivo');
const vistaPrevia = document.getElementById('vista-previa');
const btnSubir    = document.getElementById('btn-subir');
const mensaje     = document.getElementById('mensaje');
const galeria     = document.getElementById('galeria');

let foto = null; // la imagen lista para subir (venga de la cámara o del archivo)

function mostrar(texto, clase) {
    mensaje.textContent = texto;
    mensaje.className = clase;
    mensaje.hidden = false;
}

// Las dos opciones terminan aquí: guardan la foto y muestran la vista previa[cite: 9].
function usarFoto(imagen) {
    foto = imagen;
    vistaPrevia.src = URL.createObjectURL(imagen);
    vistaPrevia.hidden = false;
    btnSubir.disabled = false;
}

// ---------- Opción 1: cámara en vivo ----------
btnCamara.addEventListener('click', async () => {
    if (!navigator.mediaDevices) {
        mostrar('La cámara solo funciona en https:// o en localhost. Usa la opción de archivo.', 'error');
        return;
    }
    try {
        video.srcObject = await navigator.mediaDevices.getUserMedia({ video: true });
        video.hidden = false;
        btnTomar.disabled = false;
    } catch (error) {
        mostrar('No se pudo abrir la cámara: ' + error.message, 'error');
    }
});

btnTomar.addEventListener('click', () => {
    const lienzo = document.createElement('canvas');
    lienzo.width = video.videoWidth;
    lienzo.height = video.videoHeight;
    lienzo.getContext('2d').drawImage(video, 0, 0);
    lienzo.toBlob(usarFoto, 'image/jpeg', 0.9);
});

// ---------- Opción 2: archivo del equipo ----------
archivo.addEventListener('change', () => {
    if (archivo.files[0]) usarFoto(archivo.files[0]);
});

// ---------- Subir: el mismo servicio para las dos opciones ----------
btnSubir.addEventListener('click', async () => {
    if (!foto) {
        mostrar('Por favor selecciona o toma una foto primero.', 'error');
        return;
    }

    const datos = new FormData();
    datos.append('foto', foto, 'foto.jpg');

    btnSubir.disabled = true;
    mostrar('Subiendo foto...', '');

    try {
        const respuesta = await fetch('api/subir.php', { method: 'POST', body: datos });
        const json = await respuesta.json();
        
        if (!respuesta.ok) {
            btnSubir.disabled = false;
            mostrar(json.error || 'Error al subir la imagen.', 'error');
            return;
        }

        mostrar('¡Foto guardada correctamente como ' + json.archivo + '!', 'exito');
        
        // Limpiar vista previa, desactivar botón y limpiar input
        vistaPrevia.src = '';
        vistaPrevia.hidden = true;
        foto = null;
        archivo.value = '';
        btnSubir.disabled = true;

        // Recargar la galería de inmediato para que aparezca abajo
        await cargarGaleria();
    } catch (error) {
        btnSubir.disabled = false;
        mostrar('Error de red al subir la foto: ' + error.message, 'error');
    }
});

// ---------- Galería: pide la lista al servicio y la dibuja ----------
async function cargarGaleria() {
    try {
        const respuesta = await fetch('api/fotos.php');
        if (!respuesta.ok) throw new Error('No se pudo obtener la galería.');
        
        const fotos = await respuesta.json();
        galeria.innerHTML = '';
        
        if (fotos.length === 0) {
            galeria.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #64748b;">No hay fotos todavía.</p>';
            return;
        }

        for (const f of fotos) {
            const figura = document.createElement('figure');
            const img = document.createElement('img');
            const pie = document.createElement('figcaption');
            
            img.src = f.url;
            img.alt = 'Foto de ' + f.usuario;
            pie.textContent = f.usuario; // textContent evita XSS[cite: 9]
            
            figura.append(img, pie);
            galeria.append(figura);
        }
    } catch (error) {
        console.error('Error al cargar la galería:', error);
    }
}

// Cargar la galería automáticamente al abrir la página
cargarGaleria();
