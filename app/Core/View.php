<?php
/**
 * app/Core/View.php · Renderizador de vistas del MVC nativo (TP10).
 * Mantiene el HTML separado de la lógica del controlador.
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private string $directorio;

    public function __construct(string $directorio)
    {
        $this->directorio = rtrim($directorio, '/');
    }

    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $vista Ruta relativa, por ejemplo "tienda/index".
     * @param array<string,mixed> $datos Variables disponibles en la vista.
     */
    public function render(string $vista, array $datos = [], ?string $layout = 'layouts/app'): string
    {
        $archivo = $this->directorio . '/' . $vista . '.php';
        if (!is_file($archivo)) {
            throw new RuntimeException('No se encontró la vista: ' . $vista);
        }

        extract($datos, EXTR_SKIP);

        ob_start();
        require $archivo;
        $contenido = (string) ob_get_clean();

        if ($layout === null) {
            return $contenido;
        }

        $archivoLayout = $this->directorio . '/' . $layout . '.php';
        if (!is_file($archivoLayout)) {
            return $contenido;
        }

        ob_start();
        require $archivoLayout;
        return (string) ob_get_clean();
    }
}
