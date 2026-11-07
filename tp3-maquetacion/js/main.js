/**
 * TechFix · TP3
 * main.js · comportamiento general del sitio maquetado.
 */
(function () {
  "use strict";

  document.documentElement.classList.add("js");

  /* Año actual en el pie de página --------------------------------------- */
  document.querySelectorAll("[data-anio]").forEach(function (nodo) {
    nodo.textContent = String(new Date().getFullYear());
  });

  /* Aparición suave de bloques ------------------------------------------- */
  var bloques = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && bloques.length) {
    var observador = new IntersectionObserver(
      function (entradas) {
        entradas.forEach(function (entrada) {
          if (entrada.isIntersecting) {
            entrada.target.classList.add("is-visible");
            observador.unobserve(entrada.target);
          }
        });
      },
      { threshold: 0.12 }
    );

    bloques.forEach(function (bloque) {
      observador.observe(bloque);
    });
  } else {
    bloques.forEach(function (bloque) {
      bloque.classList.add("is-visible");
    });
  }

  /* Contador del carrito compartido -------------------------------------- */
  function leerCarrito() {
    var total = 0;
    try {
      total = parseInt(window.localStorage.getItem("tf_carrito") || "0", 10);
    } catch (error) {
      total = 0;
    }
    return Number.isFinite(total) && total > 0 ? total : 0;
  }

  function pintarContador() {
    var total = leerCarrito();
    document.querySelectorAll(".js-contador-carrito").forEach(function (nodo) {
      nodo.textContent = String(total);
      nodo.classList.toggle("d-none", total === 0);
    });
  }

  function guardarCarrito(total) {
    try {
      window.localStorage.setItem("tf_carrito", String(Math.max(0, total)));
    } catch (error) {
      /* El sitio sigue funcionando sin almacenamiento local. */
    }
    pintarContador();
  }

  window.tfCarrito = {
    leer: leerCarrito,
    guardar: guardarCarrito,
    pintar: pintarContador,
  };

  pintarContador();

  /* Aviso flotante reutilizable ------------------------------------------- */
  window.tfAviso = function (mensaje, titulo) {
    var contenedor = document.getElementById("tf-toasts");
    if (!contenedor || typeof bootstrap === "undefined") {
      return;
    }

    var toast = document.createElement("div");
    toast.className = "toast";
    toast.setAttribute("role", "status");
    toast.setAttribute("aria-live", "polite");
    toast.setAttribute("aria-atomic", "true");
    toast.innerHTML =
      '<div class="toast-header">' +
      '<span class="badge-tf me-2">TechFix</span>' +
      '<strong class="me-auto">' +
      (titulo || "Listo") +
      "</strong>" +
      '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar aviso"></button>' +
      "</div>" +
      '<div class="toast-body">' +
      mensaje +
      "</div>";

    contenedor.appendChild(toast);
    var instancia = new bootstrap.Toast(toast, { delay: 3500 });
    toast.addEventListener("hidden.bs.toast", function () {
      toast.remove();
    });
    instancia.show();
  };

  /* Validación accesible de formularios ----------------------------------- */
  document.querySelectorAll("form.needs-validation").forEach(function (form) {
    form.addEventListener("submit", function (evento) {
      if (!form.checkValidity()) {
        evento.preventDefault();
        evento.stopPropagation();
        var invalido = form.querySelector(":invalid");
        if (invalido) {
          invalido.focus();
        }
      }
      form.classList.add("was-validated");
    });

    /* Mensajes propios en español para los campos con pattern. */
    form.querySelectorAll("input[pattern]").forEach(function (campo) {
      campo.addEventListener("input", function () {
        campo.setCustomValidity("");
        if (!campo.validity.valid) {
          campo.setCustomValidity(campo.dataset.mensaje || "Revisá este campo.");
        }
      });
    });
  });

  /* Fecha mínima: hoy ------------------------------------------------------ */
  document.querySelectorAll('input[type="date"]').forEach(function (campo) {
    var hoy = new Date();
    var iso = new Date(hoy.getTime() - hoy.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
    campo.min = iso;
  });
})();
