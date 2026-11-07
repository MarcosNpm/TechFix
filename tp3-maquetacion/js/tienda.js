/**
 * TechFix · TP3
 * tienda.js · búsqueda, filtros, orden, paginación y carrito de la tienda.
 */
(function () {
  "use strict";

  var grilla = document.getElementById("grillaProductos");
  if (!grilla) {
    return;
  }

  var productos = Array.prototype.slice.call(grilla.querySelectorAll("[data-producto]"));
  var buscador = document.getElementById("buscarProducto");
  var filtro = document.getElementById("filtroCategoria");
  var orden = document.getElementById("ordenarProductos");
  var conteo = document.getElementById("resultadoConteo");
  var sinResultados = document.getElementById("sinResultados");
  var paginacion = document.getElementById("paginacion");
  var porPagina = 6;
  var paginaActual = 1;

  function normalizar(texto) {
    return (texto || "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim();
  }

  function coincide(producto) {
    var texto = normalizar(buscador.value);
    var categoria = filtro.value;
    var nombre = normalizar(producto.dataset.nombre);
    var descripcion = normalizar(producto.dataset.descripcion);
    var cumpleTexto =
      texto === "" || nombre.indexOf(texto) !== -1 || descripcion.indexOf(texto) !== -1;
    var cumpleCategoria = categoria === "todas" || producto.dataset.categoria === categoria;
    return cumpleTexto && cumpleCategoria;
  }

  function ordenar(lista) {
    var criterio = orden.value;
    return lista.slice().sort(function (a, b) {
      if (criterio === "precio-asc") {
        return Number(a.dataset.precio) - Number(b.dataset.precio);
      }
      if (criterio === "precio-desc") {
        return Number(b.dataset.precio) - Number(a.dataset.precio);
      }
      return (a.dataset.nombre || "").localeCompare(b.dataset.nombre || "", "es");
    });
  }

  function crearPaginacion(totalPaginas) {
    if (!paginacion) {
      return;
    }
    paginacion.innerHTML = "";

    if (totalPaginas <= 1) {
      return;
    }

    function agregar(texto, pagina, deshabilitado, etiqueta) {
      var li = document.createElement("li");
      li.className = "page-item" + (deshabilitado ? " disabled" : "");
      var boton = document.createElement("button");
      boton.type = "button";
      boton.className = "page-link";
      boton.textContent = texto;
      if (etiqueta) {
        boton.setAttribute("aria-label", etiqueta);
      }
      if (deshabilitado) {
        boton.disabled = true;
      }
      boton.addEventListener("click", function () {
        paginaActual = pagina;
        render();
        grilla.scrollIntoView({ behavior: "smooth", block: "start" });
      });
      li.appendChild(boton);
      paginacion.appendChild(li);
    }

    agregar("Anterior", paginaActual - 1, paginaActual === 1, "Página anterior");
    for (var i = 1; i <= totalPaginas; i += 1) {
      var li = document.createElement("li");
      li.className = "page-item" + (i === paginaActual ? " active" : "");
      var boton = document.createElement("button");
      boton.type = "button";
      boton.className = "page-link";
      boton.textContent = String(i);
      boton.setAttribute("aria-label", "Página " + i);
      if (i === paginaActual) {
        boton.setAttribute("aria-current", "page");
      }
      (function (pagina) {
        boton.addEventListener("click", function () {
          paginaActual = pagina;
          render();
          grilla.scrollIntoView({ behavior: "smooth", block: "start" });
        });
      })(i);
      li.appendChild(boton);
      paginacion.appendChild(li);
    }
    agregar("Siguiente", paginaActual + 1, paginaActual === totalPaginas, "Página siguiente");
  }

  function render() {
    var visibles = ordenar(productos.filter(coincide));
    var totalPaginas = Math.max(1, Math.ceil(visibles.length / porPagina));
    if (paginaActual > totalPaginas) {
      paginaActual = totalPaginas;
    }

    productos.forEach(function (producto) {
      producto.hidden = true;
    });

    visibles
      .slice((paginaActual - 1) * porPagina, paginaActual * porPagina)
      .forEach(function (producto) {
        producto.hidden = false;
      });

    if (conteo) {
      conteo.textContent =
        visibles.length === 1
          ? "1 producto encontrado"
          : visibles.length + " productos encontrados";
    }

    if (sinResultados) {
      sinResultados.hidden = visibles.length !== 0;
    }

    crearPaginacion(totalPaginas);
  }

  [buscador, filtro, orden].forEach(function (control) {
    if (!control) {
      return;
    }
    control.addEventListener("input", function () {
      paginaActual = 1;
      render();
    });
    control.addEventListener("change", function () {
      paginaActual = 1;
      render();
    });
  });

  grilla.addEventListener("click", function (evento) {
    var boton = evento.target.closest(".js-agregar");
    if (!boton) {
      return;
    }

    var cantidad = window.tfCarrito ? window.tfCarrito.leer() : 0;
    if (window.tfCarrito) {
      window.tfCarrito.guardar(cantidad + 1);
    }

    var original = boton.innerHTML;
    boton.innerHTML = "Agregado";
    boton.disabled = true;
    window.setTimeout(function () {
      boton.innerHTML = original;
      boton.disabled = false;
    }, 1400);

    if (typeof window.tfAviso === "function") {
      window.tfAviso(
        "<strong>" + boton.dataset.nombre + "</strong> se agregó al carrito de demostración.",
        "Producto agregado"
      );
    }
  });

  var limpiar = document.getElementById("limpiarFiltros");
  if (limpiar) {
    limpiar.addEventListener("click", function () {
      buscador.value = "";
      filtro.value = "todas";
      orden.value = "nombre";
      paginaActual = 1;
      render();
      buscador.focus();
    });
  }

  render();
})();
