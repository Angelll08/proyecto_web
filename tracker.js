document.addEventListener("DOMContentLoaded", () => {
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
});
