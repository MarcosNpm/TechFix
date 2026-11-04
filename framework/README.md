# Módulo con Fat-Free Framework · TP10 Parte B

Módulo **independiente** dentro del framework Fat-Free 3.9.3. Implementa un
reporte de stock de la tienda con rutas, controlador, modelo ORM y vista.

## Componentes

| Requisito | Archivo |
| --- | --- |
| Front controller + routing | `framework/index.php` |
| Controlador | `framework/app/controllers/ReportesController.php` |
| Modelo con ORM | `framework/app/models/Producto.php` (`DB\SQL\Mapper`) |
| Vistas con template engine | `framework/app/views/reportes/*.html` |
| Layout | `framework/app/views/layouts/*.html` |
| Configuración DB | `framework/app/config.php` |

## Rutas

| Método | URI | Acción |
| --- | --- | --- |
| GET | `/framework/` | Reporte general |
| GET | `/framework/reportes` | Reporte general |
| GET | `/framework/reportes?q=ssd` | Búsqueda con ORM |
| GET | `/framework/reportes/4` | Detalle por ruta dinámica |
| GET | `/framework/api/stock` | JSON del stock |

## Ejecución local

Desde la raíz del proyecto:

```bash
php -S 127.0.0.1:8000 router.php
```

Luego abrir:

- <http://127.0.0.1:8000/framework/>
- <http://127.0.0.1:8000/framework/reportes/4>
- <http://127.0.0.1:8000/framework/api/stock>

## Apache / XAMPP

`framework/.htaccess` reescribe las rutas hacia `index.php`. Si `mod_rewrite`
no está activo, se puede entrar directamente a `framework/index.php`.

## Notas técnicas

- Fat-Free se distribuye como archivos independientes en `framework/lib/`:
  `base.php`, `template.php`, `magic.php` y `db/`.
- No requiere Composer ni extensiones `mbstring`/`intl`.
- El modelo hereda de `DB\SQL\Mapper`; las búsquedas usan filtros con
  parámetros enlazados.
- Las plantillas usan `<repeat>`, `<check>` y escapado automático.
