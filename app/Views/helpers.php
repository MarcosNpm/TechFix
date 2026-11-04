<?php
/**
 * app/Views/helpers.php · Helpers exclusivos de las vistas MVC.
 * No contienen lógica de negocio: solo formato seguro para HTML.
 */

declare(strict_types=1);

/** Escapa cualquier valor antes de imprimirlo. */
function h(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Formatea un precio en pesos argentinos. */
function money(float|int|string|null $valor): string
{
    return '$ ' . number_format((float) $valor, 0, ',', '.');
}

/** Construye una URL interna del módulo MVC. */
function mvc_url(string $accion = 'index', array $parametros = []): string
{
    $query = array_merge(['accion' => $accion], $parametros);
    return url('mvc/index.php?' . http_build_query($query));
}

/** Devuelve la clase de stock según la cantidad disponible. */
function stock_clase(int $stock): string
{
    if ($stock <= 0) {
        return 'product-card__stock--out';
    }
    return $stock <= 3 ? 'product-card__stock--low' : '';
}

/** Texto legible del stock. */
function stock_texto(int $stock): string
{
    if ($stock <= 0) {
        return 'Sin stock';
    }
    return $stock <= 3 ? 'Últimas ' . $stock . ' unidades' : 'Disponible';
}
