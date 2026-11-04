<?php
/**
 * app/Controllers/Controlador.php · Controlador base del MVC nativo (TP10).
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

abstract class Controlador
{
    protected View $vista;

    public function __construct(View $vista)
    {
        $this->vista = $vista;
    }

    /** Renderiza una vista con su layout y devuelve el HTML. */
    protected function render(string $vista, array $datos = [], ?string $layout = 'layouts/app'): string
    {
        return $this->vista->render($vista, $datos, $layout);
    }
}
