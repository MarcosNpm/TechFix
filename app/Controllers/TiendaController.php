<?php
/**
 * app/Controllers/TiendaController.php · Controlador del catálogo (TP10).
 * Recibe la solicitud, valida parámetros, consulta el modelo y elige la vista.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Producto;

class TiendaController extends Controlador
{
    private Producto $producto;

    public function __construct(View $vista, Producto $producto)
    {
        parent::__construct($vista);
        $this->producto = $producto;
    }

    /** GET mvc/index.php?accion=index&q=...&categoria=... */
    public function index(): void
    {
        $busqueda = trim(strip_tags((string) ($_GET['q'] ?? '')));
        $busqueda = preg_replace('/\s+/u', ' ', $busqueda) ?? '';
        if (strlen($busqueda) > 60) {
            $busqueda = substr($busqueda, 0, 60);
        }

        $categoria = strtolower(trim((string) ($_GET['categoria'] ?? '')));
        $categoria = preg_replace('/[^a-z0-9-]/', '', $categoria) ?? '';

        $productos = $this->producto->buscar($busqueda, $categoria);
        $categorias = $this->producto->categorias();

        echo $this->render('tienda/index', [
            'titulo'      => 'Catálogo MVC',
            'descripcion' => 'Catálogo de repuestos refactorizado con el patrón Modelo-Vista-Controlador.',
            'productos'   => $productos,
            'categorias'  => $categorias,
            'busqueda'    => $busqueda,
            'categoria'   => $categoria,
            'total'       => count($productos),
        ]);
    }

    /** GET mvc/index.php?accion=ver&id=1 */
    public function ver(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $producto = $this->producto->encontrar((int) $id);

        if ($producto === null) {
            $this->noEncontrado('El producto solicitado no existe o está inactivo.');
            return;
        }

        echo $this->render('tienda/ver', [
            'titulo'      => $producto['nombre'],
            'descripcion' => $producto['descripcion_corta'] ?? 'Detalle del repuesto.',
            'producto'    => $producto,
        ]);
    }

    /** Respuesta 404 con una vista propia. */
    public function noEncontrado(string $mensaje = 'La página que buscás no existe.'): void
    {
        http_response_code(404);
        echo $this->render('error/404', [
            'titulo'      => 'No encontrado',
            'descripcion' => $mensaje,
            'mensaje'     => $mensaje,
        ]);
    }
}
