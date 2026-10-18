document.addEventListener("DOMContentLoaded", () => {
    checkCookieConsent();

    const sessionId = getOrCreateSessionId();
    const currentPage = window.location.pathname;
    let startTime = Date.now();

    // 1. Registrar clic y limpiar la información para que solo muestre el botón y su texto
    document.addEventListener("click", (event) => {
        let target = event.target;
        
        // Buscar si el clic pertenece a un botón, enlace o elemento interactivo
        let interactiveElement = target.closest('button, a, input[type="submit"], [data-role="button"]');
        
        if (!interactiveElement) return; // Si no dio clic en un botón o enlace relevante, lo ignoramos para mantener limpio el registro

        let tagName = interactiveElement.tagName.toLowerCase();
        let tipoElemento = tagName === 'a' ? 'enlace' : (tagName === 'input' ? 'botón de envío' : 'botón');

        // Extraer y limpiar el texto del botón
        let rawText = interactiveElement.innerText || interactiveElement.value || interactiveElement.getAttribute('aria-label') || '';
        let cleanText = rawText.replace(/\s+/g, ' ').trim().substring(0, 35);

        let descripcion = cleanText ? `Clic en ${tipoElemento}: "${cleanText}"` : `Clic en ${tipoElemento} (Sin texto)`;

        sendData({
            session_id: sessionId,
            page: currentPage,
            action_type: 'clic',
            element_details: descripcion
        });
    });

    // 2. Rastrear interacción con mapas
    const mapContainer = document.querySelector('#map, .map-container, iframe[src*="map"]');
    if (mapContainer) {
        mapContainer.addEventListener("click", (e) => {
            sendData({
                session_id: sessionId,
                page: currentPage,
                action_type: 'mapa',
                element_details: `Interacción en el mapa interactivo`
            });
        });
    }

    // 3. Registrar tiempo en la página al salir o cambiar de pestaña
    window.addEventListener("beforeunload", () => {
        let timeSpent = Math.round((Date.now() - startTime) / 1000);
        
        const data = JSON.stringify({
            session_id: sessionId,
            page: currentPage,
            action_type: 'tiempo_pagina',
            time_spent: timeSpent
        });
        
        navigator.sendBeacon('api/eventos.php', data);
    });

    function getOrCreateSessionId() {
        let cookieName = 'user_session_id';
        let match = document.cookie.match(new RegExp('(^| )' + cookieName + '=([^;]+)'));
        if (match) {
            return match[2];
        } else {
            let newId = 'sess_' + Math.random().toString(36).substring(2) + Date.now();
            document.cookie = `${cookieName}=${newId}; path=/; max-age=86400; SameSite=Lax`;
            return newId;
        }
    }

    function sendData(payload) {
        fetch('api/eventos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).catch(err => console.error('Error al registrar métrica:', err));
    }

    // Banner flotante de cookies
    function checkCookieConsent() {
        if (!document.cookie.includes('cookie_consent=true')) {
            const banner = document.createElement('div');
            banner.id = 'cookie-consent-banner';
            banner.innerHTML = `
                <div style="
                    position: fixed;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 90%;
                    max-width: 600px;
                    background: #1f2937;
                    color: #f9fafb;
                    padding: 16px 20px;
                    border-radius: 12px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
                    z-index: 99999;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    font-family: sans-serif;
                    border: 1px solid #374151;
                ">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 24px;">🍪</span>
                        <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #d1d5db;">
                            Utilizamos cookies y métricas de sesión para mejorar tu experiencia de navegación y registrar interacciones. Al hacer clic en "Aceptar", consientes su uso.
                        </p>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button id="accept-cookies-btn" style="
                            background: #2563eb;
                            color: white;
                            border: none;
                            padding: 8px 18px;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: bold;
                            font-size: 13px;
                        ">Aceptar y continuar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(banner);

            document.getElementById('accept-cookies-btn').addEventListener('click', () => {
                document.cookie = "cookie_consent=true; path=/; max-age=31536000; SameSite=Lax";
                banner.remove();
            });
        }
    }
});
