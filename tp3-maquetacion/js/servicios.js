/**
 * TechFix · TP3
 * servicios.js · filtro visual del catálogo de servicios.
 */
(function () {
  "use strict";

  var botones = document.querySelectorAll(".js-filtro-servicios");
  var tarjetas = document.querySelectorAll("[data-servicio]");
  var conteo = document.getElementById("conteoServicios");

  if (!botones.length || !tarjetas.length) {
    return;
  }

  function filtrar(categoria) {
    var visibles = 0;

    tarjetas.forEach(function (tarjeta) {
      var coincide = categoria === "todos" || tarjeta.dataset.categoria === categoria;
      tarjeta.hidden = !coincide;
      if (coincide) {
        visibles += 1;
      }
    });

    botones.forEach(function (boton) {
      boton.setAttribute("aria-pressed", String(boton.dataset.categoria === categoria));
    });

    if (conteo) {
      conteo.textContent =
        visibles === 1 ? "1 servicio disponible" : visibles + " servicios disponibles";
    }
  }

  botones.forEach(function (boton) {
    boton.addEventListener("click", function () {
      filtrar(boton.dataset.categoria);
    });
  });
})();
