# TechFix · Prototipo TP4–TP10

Prototipo de alta fidelidad del sitio **TechFix** (taller de reparación de celulares,
computadoras y electrónica). Reúne las 27 pantallas que cubren el recorrido público,
la tienda, la cuenta de cliente, el panel de administración y los módulos MVC y
Fat-Free de los TP9 y TP10.

El mismo contenido se entrega en **tres formatos** que comparten una única fuente:

| | Formato | Archivo / carpeta |
| --- | --- | --- |
| **A** | Plugin de Figma | [`../figma-plugin/`](../figma-plugin/) |
| **B** | Pantallas SVG | [`svg/`](svg/) |
| **C** | Visor HTML navegable | [`index.html`](index.html) |

---

## C · Visor navegable (la forma más rápida de verlo)

El visor no requiere dependencias ni build. Solo necesita servir la carpeta por HTTP
para que el navegador pueda cargar los SVG:

```bash
cd prototipo
python3 -m http.server 8080
```

Abrir <http://localhost:8080/index.html>.

También se puede abrir `index.html` con doble clic en la mayoría de los navegadores,
pero algunos bloquean la carga de los SVG por `file://`; el servidor local evita ese
problema.

### Qué incluye el visor

- Barra lateral agrupada por **Sistema**, **Escritorio** y **Móvil**.
- Buscador de pantallas por nombre o slug.
- Navegación con **← / →**, botones **Anterior / Siguiente** y contador.
- Recorrido guiado: Inicio → Servicios → Solicitud → Seguimiento → Tienda → Mi panel → Admin.
- Enlaces profundos: `index.html#07-tienda`, `index.html#14-admin-panel`, etc.
- Botón **Abrir SVG** para ver la pantalla aislada.

---

## B · Pantallas SVG

La carpeta [`svg/`](svg/) contiene un archivo por pantalla con el nombre del slug:

```
svg/01-inicio.svg
svg/02-servicios.svg
svg/07-tienda.svg
...
```

Son SVG vectoriales con `viewBox`, texto real y grupos editables. Se pueden abrir en
Illustrator, Inkscape, Figma, un navegador o incrustar en una presentación.

Los encabezados incluyen enlaces reales (`<a>` con `target="_top"`) a
`../index.html#slug`, de modo que al abrir un SVG en el navegador el menú del sitio
también navega entre pantallas.

Para regenerar todo desde el código:

```bash
cd prototipo
python3 generar.py
```

El generador reescribe `svg/`, `screens.json`, `index.html`, y los archivos del plugin
en `../figma-plugin/`. No requiere paquetes externos: solo Python 3.

---

## A · Plugin de Figma

El plugin inserta las 27 pantallas en el archivo de Figma, cada una como un frame
vectorial con sus capas y textos. No hay que diseñar nada a mano.

### Requisitos

- Cuenta de Figma (web o escritorio).
- El archivo de destino abierto, por ejemplo:
  <https://www.figma.com/design/b1An3nGup8IbJxsslpADP1/Sin-t%C3%ADtulo>

### Pasos

1. Abrir Figma y entrar al archivo donde se quiere el prototipo.
2. Menú **Plugins → Development → Import plugin from manifest…**
3. Seleccionar el archivo `figma-plugin/manifest.json` de este repositorio.
4. Ejecutar **Plugins → Development → TechFix · Prototipo**.
5. El plugin:
   - crea (o reutiliza) una página llamada **`TechFix · Prototipo`**;
   - borra su contenido anterior para permitir reejecuciones;
   - inserta las 27 pantallas en una grilla de 3 columnas, agrupadas por sección;
   - agrega una etiqueta con número y nombre sobre cada pantalla;
   - hace zoom para encuadrar todo el conjunto.

Al finalizar muestra un aviso: `TechFix: 27 pantallas insertadas…`.

### Notas

- Las pantallas mantienen el tamaño real de diseño (1440 px de ancho y 390 px para móvil),
  por lo que se pueden presentar a escala 1:1 o reducirlas con **K**.
- El plugin usa `figma.createNodeFromSvg()`, así que cada pantalla queda como vectores y
  textos editables, no como una imagen plana.
- Si Figma pide permisos de desarrollo la primera vez, aceptar; el plugin corre localmente
  y no envía datos a ningún servidor.
- Para volver a generarlo después de cambiar el diseño, ejecutar `python3 generar.py` y
  correr el plugin otra vez.

---

## Las 27 pantallas

| # | Slug | Pantalla | Grupo | Tamaño |
| --- | --- | --- | --- | --- |
| 01 | `00-sistema-diseno` | Sistema de diseño | Sistema | 1440×1000 |
| 02 | `01-inicio` | Inicio | Escritorio | 1440×1140 |
| 03 | `02-servicios` | Servicios | Escritorio | 1440×1800 |
| 04 | `03-servicio-detalle` | Cambio de pantalla | Escritorio | 1440×1240 |
| 05 | `04-solicitud` | Solicitud de reparación | Escritorio | 1440×1360 |
| 06 | `05-confirmacion` | Solicitud confirmada | Escritorio | 1440×1140 |
| 07 | `06-seguimiento` | Seguimiento | Escritorio | 1440×1240 |
| 08 | `07-tienda` | Tienda de repuestos | Escritorio | 1440×1420 |
| 09 | `08-carrito` | Carrito | Escritorio | 1440×1040 |
| 10 | `09-contacto` | Contacto | Escritorio | 1440×1240 |
| 11 | `10-sobre-el-taller` | Sobre el taller | Escritorio | 1440×1420 |
| 12 | `11-login` | Ingresar | Escritorio | 1440×900 |
| 13 | `12-registro` | Crear cuenta | Escritorio | 1440×1000 |
| 14 | `13-dashboard-cliente` | Mi panel | Escritorio | 1440×1290 |
| 15 | `14-admin-panel` | Panel de administración | Escritorio | 1440×1290 |
| 16 | `15-admin-servicios` | Gestión de servicios | Escritorio | 1440×1138 |
| 17 | `16-admin-reparaciones` | Gestión de reparaciones | Escritorio | 1440×1084 |
| 18 | `17-admin-usuarios` | Usuarios y roles | Escritorio | 1440×1030 |
| 19 | `18-mvc-catalogo` | Catálogo MVC | Escritorio | 1440×1120 |
| 20 | `19-mvc-detalle` | Detalle MVC | Escritorio | 1440×900 |
| 21 | `20-framework-reporte` | Reporte de stock | Escritorio | 1440×900 |
| 22 | `21-framework-detalle` | Detalle de stock | Escritorio | 1440×850 |
| 23 | `22-404` | Página no encontrada | Escritorio | 1440×940 |
| 24 | `23-mapa-sitio` | Mapa del sitio | Escritorio | 1440×1140 |
| 25 | `24-mobile-inicio` | Inicio móvil | Móvil | 390×844 |
| 26 | `25-mobile-tienda` | Tienda móvil | Móvil | 390×844 |
| 27 | `26-mobile-solicitud` | Solicitud móvil | Móvil | 390×844 |

---

## Sistema de diseño

- **Paleta:** `#1A1A2E` (tinta), `#00A8CC` (cian), `#F4F7F9` (fondo), `#FFFFFF` (blanco),
  `#333333` (texto).
- **Estados:** cian para “Recibido”, violeta para “En reparación”, verde para “Listo”,
  gris para “Entregado”.
- **Tipografía:** Inter / sistema, títulos de 38–54 px, cuerpo de 11–16 px,
  metadatos de 8–10 px con mayúsculas y tracking.
- **Componentes:** header de dos barras, footer de cuatro columnas, tarjetas con sombra
  suave, chips de estado, inputs de 44 px, botones tipo píldora.
- **Radios:** 10–20 px en tarjetas, 999 px en botones y chips.

La pantalla `00-sistema-diseno` muestra la paleta, la jerarquía tipográfica, los botones,
los estados y los formularios en una sola vista.

---

## Estructura de archivos

```
prototipo/
├── generar.py              # orquestador: SVG + manifiesto + visor + plugin
├── proto_lib.py            # tokens, primitivas SVG, header/footer, iconos
├── screens_public.py       # sistema, home, servicios, solicitud, confirmación
├── screens_shop.py         # seguimiento, tienda, carrito, contacto, cuenta, 404
├── screens_admin.py        # panel, CRUD, MVC, Fat-Free y pantallas móviles
├── plantilla_visor.html    # plantilla del visor (se rellena al generar)
├── screens.json            # manifiesto de las 27 pantallas
├── index.html              # visor navegable ya generado
└── svg/                    # 27 SVG generados
figma-plugin/
├── manifest.json           # manifiesto del plugin de desarrollo
└── code.js                 # plugin con los 27 SVG embebidos
```

---

## Credenciales y datos de ejemplo

Los textos del prototipo usan los mismos datos que el sitio PHP:

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@techfix.com.ar` | `Admin1234!` |
| Cliente | `cliente@demo.com` | `Cliente1234!` |

Código de seguimiento de ejemplo: **`TF-260901-A1B2C3`**.

---

## Relación con los trabajos prácticos

| TP | Pantallas del prototipo |
| --- | --- |
| TP4 | Inicio, servicios, sobre el taller, contacto, 404, mapa del sitio |
| TP5 | Solicitud, confirmación, login, registro y estados de validación |
| TP6 | Modelo reflejado en tienda, carrito, pedidos y panel |
| TP7 | Panel admin, gestión de servicios, reparaciones y usuarios |
| TP8 | Login, registro, mi panel y vista protegida de administración |
| TP9 | Tienda con búsqueda y actualización dinámica (API JSON) |
| TP10 | Catálogo MVC, detalle MVC, reporte Fat-Free y detalle Fat-Free |

La implementación funcional de cada TP está en el código PHP del repositorio; este
prototipo documenta la interfaz y el flujo de navegación.
