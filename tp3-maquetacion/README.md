# TechFix · TP3 · Maquetación estática

Sitio estático de **TechFix** con las **diez vistas** definidas en el TP3, maquetadas en
HTML5 con **Bootstrap 5.3.3** y una hoja de estilos propia. Es la base visual que después
se migró a PHP en el TP4.

## Vistas

| N.º | Archivo                | Sección                 | Contenido principal                                                       |
| --- | ---------------------- | ----------------------- | ------------------------------------------------------------------------- |
| 1   | `index.html`           | Inicio                  | Hero, servicios destacados, proceso, garantía, opiniones y contacto.      |
| 2   | `servicios.html`       | Servicios               | Grilla de diez categorías, filtros y espacio publicitario.                |
| 3   | `servicio.html`        | Detalles de reparación  | Imagen, descripción, inclusions, precio y servicios relacionados.         |
| 4   | `solicitud.html`       | Solicitud de reparación | Datos del equipo y del cliente, términos y confirmación.                  |
| 5   | `carrito.html`         | Carrito                 | Productos, cantidades, resumen, pago simulado y botón de arrepentimiento. |
| 6   | `confirmacion.html`    | Confirmación            | Número de orden, resumen y siguientes pasos.                              |
| 7   | `seguimiento.html`     | Seguimiento             | Código de reparación, línea de estados y observaciones del técnico.       |
| 8   | `tienda.html`          | Tienda                  | Buscador, filtros, orden, paginación y carrito.                           |
| 9   | `sobre-el-taller.html` | Sobre el taller         | Historia, galería, valores y equipo.                                      |
| 10  | `contacto.html`        | Contacto                | Formulario, datos del taller, mapa esquemático y preguntas frecuentes.    |

## Estructura

```text
tp3-maquetacion/
├── index.html
├── servicios.html
├── servicio.html
├── solicitud.html
├── carrito.html
├── confirmacion.html
├── seguimiento.html
├── tienda.html
├── sobre-el-taller.html
├── contacto.html
├── css/
│   ├── bootstrap.min.css   # Bootstrap 5.3.3 (MIT)
│   └── estilos.css         # identidad visual TechFix
├── js/
│   ├── bootstrap.bundle.min.js
│   ├── main.js             # navegación, avisos y validación general
│   ├── formularios.js      # respuesta de contacto y solicitud
│   ├── tienda.js           # búsqueda, filtros, orden, paginación y carrito
│   ├── carrito.js          # cantidades, totales y pago simulado
│   ├── seguimiento.js      # consulta de estados
│   ├── servicios.js        # filtro del catálogo
│   └── confirmacion.js     # datos de la demo e impresión
├── img/                    # ilustraciones y favicon en SVG
├── package.json
├── .prettierrc
└── .prettierignore
```

## Cómo verlo

No necesita PHP ni base de datos. Desde esta carpeta:

```bash
python3 -m http.server 8080
```

Luego abrir <http://localhost:8080/index.html>.

> Los formularios y el pago son **demostraciones estáticas**: validan los datos y muestran
> la respuesta, pero no envían información a ningún servidor.

## Bootstrap y estilos propios

- Bootstrap se sirve **localmente** desde `css/` y `js/` para que el sitio funcione sin
  depender de una CDN y para que GitHub Pages lo publique sin configuración extra.
- Se usan componentes de Bootstrap: navbar responsive, grid, botones, formularios,
  validación, tarjetas, paginación, barras de progreso, modales, acordeón y toasts.
- `css/estilos.css` define la paleta del TP3 (`#1A1A2E`, `#00A8CC`, `#F4F7F9`, `#FFFFFF`,
  `#333333`), la tipografía, los componentes propios y los ajustes de accesibilidad.

## Formato con Prettier

```bash
npm install
npm run format        # formatea HTML, CSS, JS, JSON y Markdown
npm run format:check  # verifica sin modificar
```

La configuración está en `.prettierrc` (ancho 100, comillas dobles, punto y coma, LF).

## Accesibilidad

- Enlace «Saltar al contenido principal» y landmarks (`header`, `nav`, `main`, `footer`).
- Un solo `h1` por página y jerarquía de encabezados sin saltos.
- Etiquetas asociadas a todos los campos, mensajes de error y foco automático en el primer
  campo inválido.
- Contraste mínimo AA, estados de foco visibles y navegación completa por teclado.
- `aria-current="page"`, `aria-pressed`, `aria-live`, `role="progressbar"` y `role="img"`
  donde corresponde.
- Respeto por `prefers-reduced-motion`.

El informe completo está en [`../docs/LIGHTHOUSE.md`](../docs/LIGHTHOUSE.md).

## Publicación

El repositorio incluye un flujo de GitHub Actions que publica esta carpeta en GitHub Pages
con cada push a `main`. Las instrucciones están en
[`../docs/DESPLIEGUE.md`](../docs/DESPLIEGUE.md).
