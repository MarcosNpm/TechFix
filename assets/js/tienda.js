/* ============================================================
   TechFix · tienda.js (TP9)
   Búsqueda asincrónica con Fetch API y actualización del DOM
   sin recargar la página.
   ============================================================ */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const buscador = document.getElementById('busquedaProductos');
        const contenedor = document.getElementById('resultadosProductos');
        const contador = document.getElementById('contadorProductos');
        const estado = document.getElementById('estadoBusqueda');
        const filtros = document.querySelectorAll('[data-categoria-producto]');

        if (!buscador || !contenedor || !contenedor.dataset.api) {
            return;
        }

        const api = contenedor.dataset.api;
        const base = contenedor.dataset.base || '';
        const csrf = contenedor.dataset.csrf || '';
        let categoria = contenedor.dataset.categoria || '';
        let temporizador = null;
        let controlador = null;

        function crearTarjeta(producto) {
            const articulo = document.createElement('article');
            articulo.className = 'product-card';

            const media = document.createElement('div');
            media.className = 'product-card__media';

            const badge = document.createElement('span');
            badge.className = 'product-card__badge';
            badge.textContent = producto.categoria || 'Repuesto';

            const imagen = document.createElement('img');
            imagen.src = base + 'assets/img/' + (producto.imagen || 'producto-generico.svg');
            imagen.alt = producto.nombre;
            imagen.loading = 'lazy';

            media.appendChild(badge);
            media.appendChild(imagen);

            const cuerpo = document.createElement('div');
            cuerpo.className = 'product-card__body';

            const titulo = document.createElement('h3');
            titulo.textContent = producto.nombre;

            const descripcion = document.createElement('p');
            descripcion.className = 'product-card__desc';
            descripcion.textContent = producto.descripcion_corta || '';

            const pie = document.createElement('div');
            pie.className = 'product-card__foot';

            const bloquePrecio = document.createElement('div');
            const precio = document.createElement('span');
            precio.className = 'product-card__price';
            precio.textContent = '$ ' + Number(producto.precio).toLocaleString('es-AR');
            const stock = document.createElement('span');
            stock.className = 'product-card__stock';
            if (Number(producto.stock) <= 0) {
                stock.classList.add('product-card__stock--out');
                stock.textContent = 'Sin stock';
            } else if (Number(producto.stock) <= 3) {
                stock.classList.add('product-card__stock--low');
                stock.textContent = 'Últimas ' + producto.stock + ' unidades';
            } else {
                stock.textContent = 'Disponible';
            }

            bloquePrecio.appendChild(precio);
            bloquePrecio.appendChild(stock);

            const formulario = document.createElement('form');
            formulario.method = 'post';
            formulario.action = base + 'carrito.php';

            const campoCsrf = document.createElement('input');
            campoCsrf.type = 'hidden';
            campoCsrf.name = '_csrf';
            campoCsrf.value = csrf;

            const accion = document.createElement('input');
            accion.type = 'hidden';
            accion.name = 'accion';
            accion.value = 'agregar';

            const id = document.createElement('input');
            id.type = 'hidden';
            id.name = 'producto_id';
            id.value = producto.id;

            const cantidad = document.createElement('input');
            cantidad.type = 'hidden';
            cantidad.name = 'cantidad';
            cantidad.value = '1';

            const boton = document.createElement('button');
            boton.type = 'submit';
            boton.className = 'product-card__add';
            boton.setAttribute('aria-label', 'Agregar ' + producto.nombre + ' al carrito');
            boton.disabled = Number(producto.stock) <= 0;
            boton.innerHTML = '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';

            formulario.appendChild(campoCsrf);
            formulario.appendChild(accion);
            formulario.appendChild(id);
            formulario.appendChild(cantidad);
            formulario.appendChild(boton);

            pie.appendChild(bloquePrecio);
            pie.appendChild(formulario);

            cuerpo.appendChild(titulo);
            cuerpo.appendChild(descripcion);
            cuerpo.appendChild(pie);

            articulo.appendChild(media);
            articulo.appendChild(cuerpo);
            return articulo;
        }

        function mostrarCargando() {
            if (estado) {
                estado.innerHTML = '<span class="loading-text"><span class="spinner"></span> Buscando repuestos…</span>';
            }
        }

        function mostrarError(mensaje) {
            contenedor.innerHTML = '';
            const caja = document.createElement('div');
            caja.className = 'empty-state';
            caja.innerHTML = '<h3>No pudimos completar la búsqueda</h3>';
            const p = document.createElement('p');
            p.textContent = mensaje;
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'btn btn--outline';
            boton.textContent = 'Reintentar';
            boton.addEventListener('click', function () { buscar(buscador.value); });
            caja.appendChild(p);
            caja.appendChild(boton);
            contenedor.appendChild(caja);
            if (estado) estado.textContent = '';
        }

        function renderizar(productos, total) {
            contenedor.innerHTML = '';

            if (!productos.length) {
                const vacio = document.createElement('div');
                vacio.className = 'empty-state';
                vacio.innerHTML = '<h3>Sin resultados</h3><p>Probá con otra palabra o quitá los filtros.</p>';
                contenedor.appendChild(vacio);
                if (contador) contador.textContent = '0';
                return;
            }

            const fragmento = document.createDocumentFragment();
            productos.forEach(function (producto) {
                fragmento.appendChild(crearTarjeta(producto));
            });
            contenedor.appendChild(fragmento);

            if (contador) contador.textContent = String(total);
            if (estado) estado.textContent = '';
        }

        async function buscar(texto) {
            if (controlador) {
                controlador.abort();
            }
            controlador = new AbortController();
            mostrarCargando();

            const parametros = new URLSearchParams();
            if (texto.trim() !== '') parametros.set('q', texto.trim());
            if (categoria !== '') parametros.set('categoria', categoria);

            try {
                const respuesta = await fetch(api + '?' + parametros.toString(), {
                    headers: { 'Accept': 'application/json' },
                    signal: controlador.signal
                });

                if (!respuesta.ok) {
                    throw new Error('El servidor respondió con el código ' + respuesta.status + '.');
                }

                const datos = await respuesta.json();
                if (datos.status !== 'success') {
                    throw new Error(datos.message || 'La respuesta del servidor no es válida.');
                }

                renderizar(datos.data || [], datos.total || 0);
            } catch (error) {
                if (error.name === 'AbortError') return;
                mostrarError('Revisá tu conexión e intentá nuevamente.');
                console.error('Error en la búsqueda AJAX:', error);
            }
        }

        buscador.addEventListener('input', function (evento) {
            window.clearTimeout(temporizador);
            const texto = evento.target.value;
            temporizador = window.setTimeout(function () { buscar(texto); }, 280);
        });

        filtros.forEach(function (filtro) {
            filtro.addEventListener('click', function () {
                filtros.forEach(function (f) { f.classList.remove('active'); });
                filtro.classList.add('active');
                categoria = filtro.getAttribute('data-categoria-producto') || '';
                buscar(buscador.value);
            });
        });

        const form = document.getElementById('formBusquedaProductos');
        if (form) {
            form.addEventListener('submit', function (evento) {
                evento.preventDefault();
                buscar(buscador.value);
            });
        }
    });
})();
