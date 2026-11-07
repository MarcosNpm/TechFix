/**
 * TechFix · TP3
 * carrito.js · cantidades, totales y pago simulado del carrito.
 */
(function () {
  "use strict";

  var contenedor = document.getElementById("carritoContenido");
  if (!contenedor) {
    return;
  }

  var vacio = document.getElementById("carritoVacio");
  var subtotalNodo = document.getElementById("subtotal");
  var envioNodo = document.getElementById("envio");
  var totalNodo = document.getElementById("total");
  var cantidadNodo = document.getElementById("cantidadItems");
  var formPago = document.getElementById("formPago");
  var botonPago = document.getElementById("botonPagar");
  var envioGratisDesde = 80000;
  var costoEnvio = 6500;

  var formato = new Intl.NumberFormat("es-AR", {
    style: "currency",
    currency: "ARS",
    maximumFractionDigits: 0,
  });

  function items() {
    return Array.prototype.slice.call(contenedor.querySelectorAll("[data-item]"));
  }

  function recalcular() {
    var subtotal = 0;
    var unidades = 0;

    items().forEach(function (item) {
      var precio = Number(item.dataset.precio) || 0;
      var cantidad = parseInt(item.querySelector(".js-cantidad").value || "1", 10);
      cantidad = Math.min(99, Math.max(1, cantidad));
      item.querySelector(".js-cantidad").value = String(cantidad);
      item.querySelector(".js-linea").textContent = formato.format(precio * cantidad);
      subtotal += precio * cantidad;
      unidades += cantidad;
    });

    var envio = subtotal === 0 || subtotal >= envioGratisDesde ? 0 : costoEnvio;
    var total = subtotal + envio;

    subtotalNodo.textContent = formato.format(subtotal);
    envioNodo.textContent = envio === 0 ? "Gratis" : formato.format(envio);
    totalNodo.textContent = formato.format(total);
    cantidadNodo.textContent = unidades === 1 ? "1 unidad" : unidades + " unidades";

    var hayItems = items().length > 0;
    contenedor.hidden = !hayItems;
    if (vacio) {
      vacio.hidden = hayItems;
    }
    if (botonPago) {
      botonPago.disabled = !hayItems;
    }

    if (window.tfCarrito) {
      window.tfCarrito.guardar(unidades);
    }
  }

  contenedor.addEventListener("click", function (evento) {
    var item = evento.target.closest("[data-item]");
    if (!item) {
      return;
    }

    var input = item.querySelector(".js-cantidad");
    var cantidad = parseInt(input.value || "1", 10);

    if (evento.target.closest(".js-mas")) {
      input.value = String(Math.min(99, cantidad + 1));
      recalcular();
    }

    if (evento.target.closest(".js-menos")) {
      input.value = String(Math.max(1, cantidad - 1));
      recalcular();
    }

    if (evento.target.closest(".js-eliminar")) {
      item.remove();
      recalcular();
      if (typeof window.tfAviso === "function") {
        window.tfAviso("El producto se quitó del carrito.", "Carrito actualizado");
      }
    }
  });

  contenedor.addEventListener("input", function (evento) {
    if (evento.target.classList.contains("js-cantidad")) {
      recalcular();
    }
  });

  var vaciar = document.getElementById("vaciarCarrito");
  if (vaciar) {
    vaciar.addEventListener("click", function () {
      items().forEach(function (item) {
        item.remove();
      });
      recalcular();
      if (typeof window.tfAviso === "function") {
        window.tfAviso("El carrito quedó vacío.", "Carrito actualizado");
      }
    });
  }

  if (formPago) {
    formPago.addEventListener("submit", function (evento) {
      evento.preventDefault();
      evento.stopPropagation();

      if (!formPago.checkValidity()) {
        formPago.classList.add("was-validated");
        var invalido = formPago.querySelector(":invalid");
        if (invalido) {
          invalido.focus();
        }
        return;
      }

      try {
        window.sessionStorage.setItem("tf_total", totalNodo.textContent);
      } catch (error) {
        /* La confirmación muestra un total genérico. */
      }

      botonPago.disabled = true;
      botonPago.textContent = "Procesando pago…";
      window.setTimeout(function () {
        window.location.href = "confirmacion.html";
      }, 900);
    });
  }

  recalcular();
})();
