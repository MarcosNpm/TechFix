/**
 * TechFix · TP3
 * seguimiento.js · consulta simulada del estado de una reparación.
 */
(function () {
  "use strict";

  var form = document.getElementById("formSeguimiento");
  if (!form) {
    return;
  }

  var campo = document.getElementById("codigo");
  var resultado = document.getElementById("resultadoSeguimiento");
  var error = document.getElementById("errorSeguimiento");
  var codigoDemo = "TF-260901-A1B2C3";

  function normalizar(valor) {
    return (valor || "").trim().toUpperCase().replace(/\s+/g, "");
  }

  function consultar(codigo) {
    error.classList.add("d-none");
    resultado.classList.add("d-none");

    if (codigo === "") {
      campo.setCustomValidity("Ingresá el código de reparación.");
      campo.reportValidity();
      return;
    }

    campo.setCustomValidity("");

    if (codigo === codigoDemo) {
      resultado.classList.remove("d-none");
      resultado.setAttribute("tabindex", "-1");
      resultado.focus({ preventScroll: true });
      resultado.scrollIntoView({ behavior: "smooth", block: "start" });
      return;
    }

    error.classList.remove("d-none");
    error.setAttribute("tabindex", "-1");
    error.focus({ preventScroll: true });
    error.scrollIntoView({ behavior: "smooth", block: "center" });
  }

  campo.addEventListener("input", function () {
    campo.setCustomValidity("");
  });

  form.addEventListener("submit", function (evento) {
    evento.preventDefault();
    evento.stopPropagation();
    consultar(normalizar(campo.value));
  });

  var ejemplo = document.getElementById("verEjemplo");
  if (ejemplo) {
    ejemplo.addEventListener("click", function () {
      campo.value = codigoDemo;
      consultar(codigoDemo);
    });
  }

  if (window.location.hash === "#demo") {
    campo.value = codigoDemo;
    consultar(codigoDemo);
  }
})();
