# Módulo MVC nativo · TP10 Parte A

Refactorización del catálogo de productos del sitio TechFix usando el patrón
Modelo-Vista-Controlador en PHP puro.

## Estructura

```
app/
├── Models/
│   ├── Modelo.php          # Conexión PDO y ejecución de consultas preparadas
│   └── Producto.php        # Consultas del catálogo (buscar, encontrar, categorías)
├── Controllers/
│   ├── Controlador.php     # Base con acceso al renderizador
│   └── TiendaController.php# Procesa q, categoria e id; elige la vista
├── Views/
│   ├── helpers.php         # h(), money(), mvc_url()
│   ├── layouts/app.php     # Layout HTML
│   ├── tienda/index.php    # Listado + búsqueda
│   ├── tienda/ver.php      # Detalle
│   └── error/404.php       # Error
└── Core/
    └── View.php            # Renderiza vistas con extract() y ob_start()
mvc/
└── index.php               # Front controller y autoload
```

## Rutas

| URI | Acción |
| --- | --- |
| `mvc/index.php` | Listado completo |
| `mvc/index.php?accion=index&q=ssd` | Búsqueda por texto |
| `mvc/index.php?accion=index&categoria=celulares` | Filtro por categoría |
| `mvc/index.php?accion=ver&id=4` | Detalle del producto |

## Separación de responsabilidades

- **Modelo:** `Producto` recibe el `PDO` por constructor. Todas las consultas usan
  `prepare()` y `execute()` con parámetros enlazados. No imprime HTML.
- **Controlador:** `TiendaController` sanitiza `$_GET`, valida el id, consulta el
  modelo, responde 404 cuando corresponde y elige la vista.
- **Vista:** solo recibe variables y genera HTML escapado con `h()`.

## Probar

```bash
php -S 127.0.0.1:8000 -t .
# Abrir http://127.0.0.1:8000/mvc/index.php
```
