/**
 * TechFix · TP3
 * confirmacion.js · datos de la demo y comprobante imprimible.
 */
(function () {
  "use strict";

  var nombre = document.getElementById("nombreCliente");
  var total = document.getElementById("totalPagado");

  try {
    var cliente = window.sessionStorage.getItem("tf_cliente");
    var monto = window.sessionStorage.getItem("tf_total");
    if (cliente && nombre) {
      nombre.textContent = cliente;
    }
    if (monto && total) {
      total.textContent = monto;
    }
  } catch (error) {
    /* Se muestran los valores por defecto. */
  }

  var imprimir = document.getElementById("imprimirComprobante");
  if (imprimir) {
    imprimir.addEventListener("click", function () {
      window.print();
    });
  }
})();
