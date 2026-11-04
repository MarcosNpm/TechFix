/* ============================================================
   TechFix · Clase 5 · Interacciones de la demo TP5
   ============================================================ */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const mensaje = document.getElementById('mensaje');
        const contador = document.querySelector('[data-contador]');

        if (mensaje && contador) {
            const actualizar = function () {
                contador.textContent = String(mensaje.value.length);
            };
            mensaje.addEventListener('input', actualizar);
            actualizar();
        }

        // Marca visualmente los campos con error y enfoca el primero.
        const primerError = document.querySelector('.is-invalid');
        if (primerError) {
            primerError.focus({ preventScroll: false });
        }
    });
})();
