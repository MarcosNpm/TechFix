/**
 * TechFix · TP3
 * formularios.js · respuesta simulada de los formularios de contacto y solicitud.
 */
(function () {
  "use strict";

  function mostrarExito(form) {
    var aviso = form.parentElement.querySelector("[data-exito]");
    if (!aviso) {
      return;
    }

    aviso.classList.remove("d-none");
    aviso.setAttribute("tabindex", "-1");
    aviso.focus({ preventScroll: true });
    aviso.scrollIntoView({ behavior: "smooth", block: "center" });

    if (typeof window.tfAviso === "function") {
      window.tfAviso(
        "El formulario se validó correctamente. Esta es una demo estática: no se envían datos.",
        "Formulario válido"
      );
    }
  }

  document.querySelectorAll('form[data-formulario="contacto"]').forEach(function (form) {
    form.addEventListener("submit", function (evento) {
      evento.preventDefault();
      evento.stopPropagation();

      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        var invalido = form.querySelector(":invalid");
        if (invalido) {
          invalido.focus();
        }
        return;
      }

      mostrarExito(form);
      form.reset();
      form.classList.remove("was-validated");
    });
  });

  document.querySelectorAll('form[data-formulario="solicitud"]').forEach(function (form) {
    form.addEventListener("submit", function (evento) {
      evento.preventDefault();
      evento.stopPropagation();

      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        var invalido = form.querySelector(":invalid");
        if (invalido) {
          invalido.focus();
        }
        return;
      }

      var nombre = form.querySelector('[name="nombre"]');
      if (nombre && nombre.value.trim() !== "") {
        try {
          window.sessionStorage.setItem("tf_cliente", nombre.value.trim().split(" ")[0]);
        } catch (error) {
          /* Sin almacenamiento: la confirmación usa un saludo genérico. */
        }
      }

      mostrarExito(form);
    });
  });
})();
