/* ============================================================
   TechFix · main.js
   Interacciones generales del sitio (menú, flashes, cantidades).
   ============================================================ */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const navToggle = document.querySelector('[data-nav-toggle]');
        const nav = document.querySelector('[data-nav]');

        if (navToggle && nav) {
            navToggle.addEventListener('click', function () {
                const abierto = nav.classList.toggle('nav--open');
                navToggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                if (!nav.contains(event.target) && !navToggle.contains(event.target)) {
                    nav.classList.remove('nav--open');
                    navToggle.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    nav.classList.remove('nav--open');
                    navToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        document.querySelectorAll('[data-cerrar-flash]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const flash = boton.closest('[data-flash]');
                if (flash) {
                    flash.style.opacity = '0';
                    flash.style.transform = 'translateX(16px)';
                    window.setTimeout(function () { flash.remove(); }, 220);
                }
            });
        });

        window.setTimeout(function () {
            document.querySelectorAll('[data-flash]').forEach(function (flash) {
                flash.style.opacity = '0';
                flash.style.transform = 'translateX(16px)';
                window.setTimeout(function () { flash.remove(); }, 220);
            });
        }, 6500);

        document.querySelectorAll('[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const mensaje = form.getAttribute('data-confirm') || '¿Confirmás esta acción?';
                if (!window.confirm(mensaje)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('[data-qty]').forEach(function (grupo) {
            const input = grupo.querySelector('input');
            const menos = grupo.querySelector('[data-qty-menos]');
            const mas = grupo.querySelector('[data-qty-mas]');
            if (!input) return;

            function cambiar(delta) {
                const min = parseInt(input.getAttribute('min') || '1', 10);
                const max = parseInt(input.getAttribute('max') || '99', 10);
                let valor = parseInt(input.value || '1', 10) + delta;
                valor = Math.max(min, Math.min(max, valor));
                input.value = valor;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (menos) menos.addEventListener('click', function () { cambiar(-1); });
            if (mas) mas.addEventListener('click', function () { cambiar(1); });
        });

        document.querySelectorAll('[data-toggle-password]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const input = document.getElementById(boton.getAttribute('data-toggle-password'));
                if (!input) return;
                const mostrar = input.type === 'password';
                input.type = mostrar ? 'text' : 'password';
                boton.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });

        const elementos = document.querySelectorAll('.reveal');
        if ('IntersectionObserver' in window && elementos.length) {
            const observador = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (entrada) {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add('visible');
                        observador.unobserve(entrada.target);
                    }
                });
            }, { threshold: 0.12 });

            elementos.forEach(function (elemento) { observador.observe(elemento); });
        } else {
            elementos.forEach(function (elemento) { elemento.classList.add('visible'); });
        }
    });
})();
