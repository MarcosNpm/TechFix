<?php
/**
 * includes/componentes.php
 * Fragmentos de interfaz reutilizables para servicios y productos.
 */

require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/iconos.php';

/** Devuelve el HTML de una tarjeta de servicio. */
function tarjeta_servicio(array $servicio): string
{
    $icono = $servicio['icono'] ?? 'herramienta';
    $urlDetalle = url('servicio.php?id=' . (int) $servicio['id']);
    $categoria = $servicio['categoria'] ?? 'Servicio';
    $descripcion = resumen((string) $servicio['descripcion'], 150);

    return '<article class="service-card reveal">'
        . '<div class="service-card__top">'
        . '<div class="service-card__icon">' . icono($icono) . '</div>'
        . '<span class="service-card__cat">' . e($categoria) . '</span>'
        . '</div>'
        . '<h3><a href="' . e($urlDetalle) . '">' . e($servicio['nombre']) . '</a></h3>'
        . '<p>' . e($descripcion) . '</p>'
        . '<div class="service-card__meta">'
        . '<span class="service-card__price">' . e(precio($servicio['precio_base'])) . '<small>Precio desde</small></span>'
        . '<a class="service-card__link" href="' . e($urlDetalle) . '">Ver detalle ' . icono('flecha') . '</a>'
        . '</div></article>';
}

/** Devuelve el HTML de una tarjeta de producto. */
function tarjeta_producto(array $producto): string
{
    $stock = (int) $producto['stock'];
    $claseStock = $stock <= 0 ? ' product-card__stock--out' : ($stock <= 3 ? ' product-card__stock--low' : '');
    $textoStock = $stock <= 0 ? 'Sin stock' : ($stock <= 3 ? 'Últimas ' . $stock . ' unidades' : 'Disponible');
    $csrf = csrf_field();
    $deshabilitado = $stock <= 0 ? ' disabled' : '';
    $imagen = $producto['imagen'] ?? 'producto-generico.svg';

    return '<article class="product-card">'
        . '<div class="product-card__media">'
        . '<span class="product-card__badge">' . e($producto['categoria'] ?? 'Repuesto') . '</span>'
        . '<img src="' . e(url('assets/img/' . $imagen)) . '" alt="' . e($producto['nombre']) . '" loading="lazy">'
        . '</div>'
        . '<div class="product-card__body">'
        . '<h3>' . e($producto['nombre']) . '</h3>'
        . '<p class="product-card__desc">' . e($producto['descripcion_corta'] ?? resumen((string) ($producto['descripcion'] ?? ''), 70)) . '</p>'
        . '<div class="product-card__foot">'
        . '<div><span class="product-card__price">' . e(precio($producto['precio'])) . '</span>'
        . '<span class="product-card__stock' . $claseStock . '">' . e($textoStock) . '</span></div>'
        . '<form method="post" action="' . e(url('carrito.php')) . '">'
        . $csrf
        . '<input type="hidden" name="accion" value="agregar">'
        . '<input type="hidden" name="producto_id" value="' . (int) $producto['id'] . '">'
        . '<input type="hidden" name="cantidad" value="1">'
        . '<button type="submit" class="product-card__add" aria-label="Agregar ' . e($producto['nombre']) . ' al carrito"' . $deshabilitado . '>'
        . icono('mas') . '</button></form>'
        . '</div></div></article>';
}

/** Imprime el mensaje de error de un campo, si existe. */
function error_campo(array $errores, string $campo): string
{
    return isset($errores[$campo])
        ? '<span class="form-error">' . e($errores[$campo]) . '</span>'
        : '';
}

/** Devuelve la clase is-invalid cuando el campo tiene error. */
function clase_error(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}

/** Imprime un badge de estado de reparación. */
function badge_estado(array $estado): string
{
    $slug = $estado['slug'] ?? $estado['estado_slug'] ?? 'neutral';
    $nombre = $estado['nombre'] ?? $estado['estado'] ?? 'Sin estado';
    return '<span class="badge ' . e(clase_estado($slug)) . '">' . e($nombre) . '</span>';
}
