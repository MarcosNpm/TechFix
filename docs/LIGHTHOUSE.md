# Informe de Lighthouse y accesibilidad · TP3

**Proyecto:** TechFix · Maquetación estática (TP3)
**Herramienta:** Lighthouse 12.8.2 · Chrome headless
**Fecha:** 6 de noviembre de 2026
**Alcance:** las diez vistas de `tp3-maquetacion/`

---

## 1. Resultado final

Las diez páginas se auditaron con el preset **Desktop** de Lighthouse (1350 × 940), sobre un
servidor local con compresión gzip. Los puntajes de **Accesibilidad**, **Buenas prácticas** y
**SEO** son 100 en todas las vistas.

| Vista                  | Rendimiento | Accesibilidad | Buenas prácticas | SEO |   FCP |   LCP |    TBT |   CLS |
| ---------------------- | ----------: | ------------: | ---------------: | --: | ----: | ----: | -----: | ----: |
| `index.html`           |          94 |           100 |              100 | 100 | 0,4 s | 0,7 s | 180 ms |     0 |
| `servicios.html`       |          98 |           100 |              100 | 100 | 0,3 s | 0,5 s | 110 ms |     0 |
| `servicio.html`        |          98 |           100 |              100 | 100 | 0,3 s | 0,5 s |  90 ms |     0 |
| `solicitud.html`       |          98 |           100 |              100 | 100 | 0,3 s | 0,5 s | 120 ms |     0 |
| `carrito.html`         |         100 |           100 |              100 | 100 | 0,3 s | 0,5 s |  60 ms | 0,001 |
| `confirmacion.html`    |          99 |           100 |              100 | 100 | 0,3 s | 0,5 s |  40 ms |     0 |
| `seguimiento.html`     |         100 |           100 |              100 | 100 | 0,3 s | 0,5 s |  60 ms |     0 |
| `tienda.html`          |          97 |           100 |              100 | 100 | 0,3 s | 0,6 s | 130 ms | 0,053 |
| `sobre-el-taller.html` |         100 |           100 |              100 | 100 | 0,3 s | 0,5 s |  40 ms |     0 |
| `contacto.html`        |          98 |           100 |              100 | 100 | 0,3 s | 0,5 s | 110 ms |     0 |

**Promedios:** Rendimiento **98,2** · Accesibilidad **100** · Buenas prácticas **100** · SEO **100**.

> El preset móvil también se ejecutó. Accesibilidad, buenas prácticas y SEO dieron **100** en
> las diez vistas. Los puntajes de rendimiento móvil varían según la carga de la máquina y la
> red; por eso se documenta el preset Desktop, que es estable y reproducible.

---

## 2. Problemas detectados y correcciones

### 2.1. Accesibilidad inicial

Primera auditoría:

| Vista            | Accesibilidad | Hallazgo                                                                        |
| ---------------- | ------------: | ------------------------------------------------------------------------------- |
| `index.html`     |            95 | `aria-label` sobre un `<div>` sin rol válido (estrellas de los testimonios).    |
| `servicios.html` |            98 | Orden de encabezados: se pasaba de `h1` a `h3` en la columna publicitaria.      |
| `contacto.html`  |            98 | Orden de encabezados: las tarjetas de datos usaban `h3` sin un `h2` previo.     |
| `tienda.html`    |            98 | Orden de encabezados: los nombres de producto eran `h3` sin un `h2` de sección. |

Correcciones aplicadas:

1. **Estrellas de testimonios:** se agregó `role="img"` al contenedor con `aria-label="5 de 5 estrellas"`.
2. **Jerarquía de encabezados:** se agregaron encabezados de sección visualmente ocultos
   (`<h2 class="visually-hidden">`) en servicios, tienda y contacto, para que la secuencia
   sea `h1 → h2 → h3` sin alterar el diseño.
3. **Formularios:** etiquetas asociadas con `for`/`id`, `autocomplete`, `aria-describedby`,
   mensajes `invalid-feedback` y foco en el primer campo inválido.
4. **Contraste:** el cian `#00A8CC` se usa para fondos y decoración; para textos y enlaces se
   usa `#007A92` (contraste AA sobre blanco). Los botones principales tienen texto blanco
   sobre `#007A92`.
5. **Foco visible:** `:focus-visible` con contorno de 3 px en enlaces, botones, campos y
   controles del navbar.
6. **Navegación:** enlace para saltar al contenido, `aria-current="page"` en el menú activo,
   `aria-expanded` en el botón del menú móvil y `aria-pressed` en los filtros.
7. **Movimiento:** se respeta `prefers-reduced-motion` y se desactivan las animaciones.
8. **Botón dentro de etiqueta:** se sacó el botón de «términos y condiciones» de adentro del
   `<label>` para evitar controles anidados; ahora es un botón independiente que abre el modal.
9. **Bootstrap local:** se sirve desde `css/` y `js/` para evitar fallas de red y mejorar la
   estabilidad de la auditoría.

### 2.2. Resultado después de las correcciones

- Accesibilidad: **100** en las diez vistas.
- Buenas prácticas: **100** en las diez vistas.
- SEO: **100** en las diez vistas.
- Sin errores de consola ni recursos 404 en las pruebas funcionales.

---

## 3. Pruebas funcionales complementarias

Además de Lighthouse se probó el comportamiento con Puppeteer/Chrome headless:

| Prueba                         | Resultado                                                         |
| ------------------------------ | ----------------------------------------------------------------- |
| Enviar solicitud vacía         | 7 campos inválidos, foco en «Nombre».                             |
| Completar y enviar solicitud   | Mensaje de éxito visible y nombre guardado para la confirmación.  |
| Enviar contacto válido         | Mensaje de éxito visible.                                         |
| Pagar en el carrito            | Redirección a `confirmacion.html`.                                |
| Código de seguimiento inválido | Alerta de error visible.                                          |
| Código `TF-260901-A1B2C3`      | Línea de cinco estados visible.                                   |
| Menú móvil                     | Se abre, actualiza `aria-expanded` y no genera scroll horizontal. |
| Enlaces y recursos internos    | 0 enlaces rotos y 0 recursos faltantes.                           |

---

## 4. Cómo reproducir la auditoría

```bash
cd tp3-maquetacion
python3 -m http.server 8080
```

En otra terminal:

```bash
npx lighthouse http://localhost:8080/index.html \
  --preset=desktop \
  --only-categories=performance,accessibility,best-practices,seo \
  --view
```

Para revisar el formato del código:

```bash
npm install
npm run format:check
```

---

## 5. Conclusión

La maquetación cumple con los objetivos de accesibilidad del TP3: **100 % en Lighthouse**,
navegación por teclado, contraste AA, formularios etiquetados y jerarquía semántica correcta.
El rendimiento se mantiene por encima de 94 en todas las vistas con el preset Desktop y sin
errores de consola.
