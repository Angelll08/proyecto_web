document.addEventListener("DOMContentLoaded", () => {
    // 0. Verificar y mostrar el banner flotante de cookies si no ha sido aceptado
    checkCookieConsent();

    const sessionId = getOrCreateSessionId();
    const currentPage = window.location.pathname;
    let startTime = Date.now();

    // 1. Registrar clic general y en elementos específicos
    document.addEventListener("click", (event) => {
        let target = event.target;
        let details = target.tagName + (target.id ? `#${target.id}` : '') + (target.className ? `.${target.className.replace(/\s+/g, '.')}` : '');
        
        sendData({
            session_id: sessionId,
            page: currentPage,
            action_type: 'clic',
            element_details: `Clic en: ${details} - Texto: ${target.innerText.substring(0, 30)}`
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
                element_details: `Interacción en mapa en las coordenadas X:${e.clientX} Y:${e.clientY}`
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

    // Función que crea y muestra el aviso flotante interactivo
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
                            Utilizamos cookies y métricas de sesión para mejorar tu experiencia de navegación, analizar el rendimiento del sitio y registrar interacciones. Al hacer clic en "Aceptar", consientes su uso.
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
                            transition: background 0.2s;
                        ">Aceptar y continuar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(banner);

            document.getElementById('accept-cookies-btn').addEventListener('click', () => {
                // Guarda la cookie por 1 año para no volver a mostrarlo
                document.cookie = "cookie_consent=true; path=/; max-age=31536000; SameSite=Lax";
                banner.remove();
            });
        }
    }
});
