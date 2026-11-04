# TP6 · Normalización del modelo de datos de TechFix

**Alumno:** Núñez Marcos Agustín  
**Proyecto:** TechFix — taller de reparación de celulares, computadoras y electrónica  
**Base de datos:** `techfix_db`  
**Archivos relacionados:** `tp6/schema.sql` (DDL) y `tp6/consultas.sql` (DML)

---

## 1. Punto de partida: tabla sin normalizar

Para mostrar el proceso se parte de una planilla hipotética donde cada fila representa una reparación. En un mismo registro se guardan varios teléfonos del cliente, varios servicios realizados, los repuestos usados y datos que se repiten (categoría, estado, nombre del técnico).

### `reparaciones_sin_normalizar`

| codigo | cliente | telefonos | equipo | servicios_realizados | repuestos | categoria | estado | estado_descripcion | total |
|---|---|---|---|---|---|---|---|---|---|
| TF-001 | Lucía Fernández | 11-4444-2020 / 11-5555-3030 | Samsung Galaxy A54 | Cambio de pantalla, Reemplazo de batería | Pantalla OLED, Batería 3110 mAh | Celulares | Listo | El equipo está terminado | 130.000 |
| TF-002 | Julián Pérez | 11-2222-9090 | Lenovo IdeaPad 3 | Cambio de SSD, Limpieza | SSD NVMe 1 TB, Pasta térmica | Computadoras | En reparación | El técnico está trabajando | 147.000 |

Problemas detectados:

- **Grupos repetitivos:** `telefonos`, `servicios_realizados` y `repuestos` contienen listas dentro de un solo campo.
- **Datos redundantes:** la categoría y la descripción del estado se repiten en cada fila.
- **Dependencias parciales:** el precio y el nombre de un servicio dependen del servicio, no de la reparación completa.
- **Dependencias transitivas:** `estado_descripcion` depende de `estado`; `categoria` depende del servicio.
- **Dificultad para consultar:** no se puede filtrar por un servicio o un repuesto sin buscar texto dentro de una cadena.

---

## 2. Primera Forma Normal (1FN)

**Regla aplicada:** cada campo debe ser atómico; no se permiten listas ni grupos repetitivos.

Se separan los teléfonos, los servicios y los repuestos en filas independientes. La reparación deja de tener listas y pasa a identificarse con su código.

### `reparaciones_1fn`

| codigo | cliente | telefono | equipo | estado | total |
|---|---|---|---|---|---|
| TF-001 | Lucía Fernández | 11-4444-2020 | Samsung Galaxy A54 | Listo | 130.000 |
| TF-001 | Lucía Fernández | 11-5555-3030 | Samsung Galaxy A54 | Listo | 130.000 |
| TF-002 | Julián Pérez | 11-2222-9090 | Lenovo IdeaPad 3 | En reparación | 147.000 |

### `reparacion_servicios_1fn`

| codigo_reparacion | servicio | precio |
|---|---|---|
| TF-001 | Cambio de pantalla | 85.000 |
| TF-001 | Reemplazo de batería | 45.000 |
| TF-002 | Cambio de SSD y migración | 60.000 |
| TF-002 | Limpieza y mantenimiento | 35.000 |

### `reparacion_repuestos_1fn`

| codigo_reparacion | repuesto | cantidad |
|---|---|---|
| TF-001 | Pantalla OLED | 1 |
| TF-001 | Batería 3110 mAh | 1 |
| TF-002 | SSD NVMe 1 TB | 1 |

Resultado: ya no hay listas dentro de celdas. Sin embargo, todavía se repiten el cliente, el equipo y el estado en varias filas, y las tablas de servicios/repuestos mezclan datos de la reparación con datos propios del producto.

---

## 3. Segunda Forma Normal (2FN)

**Regla aplicada:** la tabla debe estar en 1FN y todos los atributos no clave deben depender de **toda** la clave primaria, no de una parte.

En `reparacion_servicios_1fn` la clave es compuesta `(codigo_reparacion, servicio)`. El `precio` depende únicamente del servicio, no de la reparación. Lo mismo ocurre con `nombre` y `categoría` del servicio.

Se separan las entidades:

### `reparaciones_2fn`

| id | codigo | cliente_id | servicio_id | estado_id | equipo | total |
|---|---|---|---|---|---|---|
| 1 | TF-260901-A1B2C3 | 2 | 1 | 4 | Samsung Galaxy A54 | 85.000 |
| 2 | TF-260902-D4E5F6 | 3 | 6 | 3 | Lenovo IdeaPad 3 | 112.000 |

### `servicios_2fn`

| id | nombre | categoria | precio_base | tiempo_estimado |
|---|---|---|---|---|
| 1 | Cambio de pantalla | Celulares | 85.000 | 2 a 4 horas |
| 2 | Reemplazo de batería | Celulares | 45.000 | 1 a 2 horas |
| 6 | Cambio de SSD y migración | Computadoras | 60.000 | 3 a 5 horas |

### `detalle_reparacion_2fn`

| reparacion_id | servicio_id | precio_al_momento |
|---|---|---|
| 1 | 1 | 85.000 |
| 2 | 6 | 60.000 |

Ahora el precio del servicio vive en `servicios` y el detalle guarda el precio histórico. La tabla de detalle depende de la clave completa `(reparacion_id, servicio_id)`.

---

## 4. Tercera Forma Normal (3FN)

**Regla aplicada:** eliminar dependencias transitivas: ningún atributo no clave puede depender de otro atributo que tampoco sea clave.

Casos detectados:

- En `servicios_2fn`, `categoria` depende del servicio, pero la descripción y el ícono dependen de la categoría. Se extrae **`categorias_servicio`**.
- En `reparaciones_2fn`, `estado_descripcion` depende de `estado_id`, no de la reparación. Se extrae **`estados_reparacion`**.
- El rol del usuario (`admin` / `usuario`) se repite en cada usuario. Se extrae **`roles`**.
- La categoría de un producto se repite en cada producto. Se extrae **`categorias_producto`**.
- Los datos del cliente se repiten en cada reparación. Se extraen a **`usuarios`** y la reparación conserva una FK opcional, además de los datos de contacto histórico.
- Las etiquetas de un servicio son una relación muchos-a-muchos. Se crean **`etiquetas`** y **`servicio_etiqueta`**.
- Los productos de un pedido son otra relación muchos-a-muchos. Se crean **`pedidos`**, **`productos`** y **`detalle_pedidos`**.

---

## 5. Modelo relacional resultante

```
roles (1) ───< usuarios (1) ───< reparaciones >─── (1) servicios >─── (1) categorias_servicio
                                      │
                                      └─── (1) estados_reparacion

categorias_producto (1) ───< productos (1) ───< detalle_pedidos >─── (1) pedidos
                                                                        │
usuarios (1) ───────────────────────────────────────────────────────────┘

servicios (N) ───< servicio_etiqueta >─── (N) etiquetas
```

### Listado de tablas, claves primarias y foráneas

| Tabla | Clave primaria | Claves foráneas | Propósito |
|---|---|---|---|
| `roles` | `id` | — | Maestro de roles (`admin`, `usuario`). |
| `usuarios` | `id` | `rol_id → roles.id` | Datos personales y credenciales. |
| `categorias_servicio` | `id` | — | Maestro de categorías de servicios. |
| `servicios` | `id` | `categoria_id → categorias_servicio.id` | Catálogo de servicios del taller. |
| `estados_reparacion` | `id` | — | Flujo de estados de una reparación. |
| `reparaciones` | `id` | `usuario_id → usuarios.id`, `servicio_id → servicios.id`, `estado_id → estados_reparacion.id` | Entidad principal del taller. |
| `categorias_producto` | `id` | — | Maestro de categorías de la tienda. |
| `productos` | `id` | `categoria_id → categorias_producto.id` | Catálogo de repuestos y accesorios. |
| `pedidos` | `id` | `usuario_id → usuarios.id` | Cabecera de una compra. |
| `detalle_pedidos` | `id` | `pedido_id → pedidos.id`, `producto_id → productos.id` | Detalle N:M de productos por pedido. |
| `etiquetas` | `id` | — | Etiquetas de servicios. |
| `servicio_etiqueta` | `(servicio_id, etiqueta_id)` | `servicio_id → servicios.id`, `etiqueta_id → etiquetas.id` | Relación N:M entre servicios y etiquetas. |
| `consultas` | `id` | — | Mensajes del formulario de contacto. |

### Claves y restricciones aplicadas

- Todas las claves primarias son `INT UNSIGNED AUTO_INCREMENT`.
- Las relaciones usan `FOREIGN KEY ... REFERENCES ...` con `ON UPDATE CASCADE`.
- Los borrados se protegen con `ON DELETE RESTRICT` en las entidades principales (no se puede borrar una categoría con servicios) y con `ON DELETE CASCADE` solamente en tablas de detalle.
- Los campos de auditoría `created_at` y `updated_at` están en todas las tablas principales.
- Se crearon índices explícitos para las búsquedas frecuentes:
  - `idx_usuarios_email` sobre `usuarios.email`.
  - `idx_servicios_categoria_activo` sobre `(categoria_id, activo)`.
  - `idx_productos_categoria_activo` sobre `(categoria_id, activo)`.
  - `idx_reparaciones_codigo` y `idx_reparaciones_email`.
  - `idx_pedidos_numero` e `idx_pedidos_estado`.

### Justificación del grado de normalización

El modelo queda en **3FN** porque:

1. Todos los campos son atómicos (1FN).
2. Todos los atributos no clave dependen de la clave primaria completa (2FN).
3. No existen dependencias transitivas: roles, categorías y estados están en tablas maestras (3FN).

Las relaciones N:M se resuelven con tablas intermedias (`detalle_pedidos` y `servicio_etiqueta`), y se conservan datos históricos importantes, como el precio del servicio al momento de la reparación y el precio unitario dentro del detalle del pedido.

---

## 6. Correspondencia con los archivos SQL

- **`tp6/schema.sql`**: crea la base `techfix_db`, las 13 tablas, PK, FK, índices y campos de auditoría.
- **`tp6/consultas.sql`**: carga roles, usuarios, categorías, servicios, estados, productos, reparaciones, pedidos y consultas de ejemplo; además incluye las operaciones CRUD de prueba (INSERT, SELECT con WHERE/ORDER BY, INNER JOIN, UPDATE y DELETE con WHERE).
