<?php
/**
 * framework/app/controllers/ReportesController.php · Controlador Fat-Free (TP10 B).
 * Recibe la solicitud desde el router, consulta el modelo ORM y renderiza
 * las vistas con el motor de plantillas del framework.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Producto;
use Base;
use DB\SQL;
use DB\SQL\Mapper;
use Template;

class ReportesController
{
    protected Base $f3;
    protected SQL $db;
    protected Producto $producto;

    public function __construct()
    {
        $this->f3 = Base::instance();
        $config = require dirname(__DIR__) . '/config.php';

        $this->db = new SQL($config['dsn'], $config['user'], $config['password']);
        $this->producto = new Producto($this->db);

        $this->f3->set('db', $this->db);
        $this->f3->set('titulo_modulo', 'Reporte de stock');
    }

    /** GET / → reporte general con búsqueda opcional. */
    public function index(): void
    {
        $texto = (string) ($this->f3->get('GET.q') ?? '');
        $texto = trim(strip_tags($texto));
        if (strlen($texto) > 60) {
            $texto = substr($texto, 0, 60);
        }

        $registros = $texto !== ''
            ? $this->producto->buscar($texto)
            : $this->producto->activos();

        $categorias = $this->mapaCategorias();
        $productos = $this->prepararFilas($registros, $categorias);

        $stockTotal = array_sum(array_column($productos, 'stock'));
        $sinStock = count(array_filter($productos, static fn(array $p): bool => $p['stock'] <= 0));
        $bajoStock = count(array_filter(
            $productos,
            static fn(array $p): bool => $p['stock'] > 0 && $p['stock'] <= 5
        ));

        $this->f3->set('titulo', $texto !== '' ? 'Búsqueda: ' . $texto : 'Reporte de stock');
        $this->f3->set('productos', $productos);
        $this->f3->set('busqueda', $texto);
        $this->f3->set('stock_total', $stockTotal);
        $this->f3->set('sin_stock', $sinStock);
        $this->f3->set('bajo_stock', $bajoStock);
        $this->f3->set('total', count($productos));

        echo Template::instance()->render('reportes/index.html');
    }

    /** GET /reportes/@id → detalle de un producto mediante ruta dinámica. */
    public function detalle(Base $f3, array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $mapa = $this->mapaCategorias();

        if ($id <= 0 || !($this->producto->activoPorId($id))) {
            $this->f3->error(404, 'El producto #' . $id . ' no existe o está inactivo.');
            return;
        }

        $producto = $this->prepararFilas([$this->producto], $mapa)[0];

        $this->f3->set('titulo', $producto['nombre']);
        $this->f3->set('producto', $producto);

        echo Template::instance()->render('reportes/detalle.html');
    }

    /** GET /api/stock → respuesta JSON del módulo (extra). */
    public function api(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $registros = $this->producto->activos();
        $productos = $this->prepararFilas($registros, $this->mapaCategorias());

        echo json_encode([
            'status' => 'success',
            'modulo' => 'Fat-Free Framework',
            'total'  => count($productos),
            'data'   => $productos,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** Mapa id => nombre de categoría, también con el ORM. */
    private function mapaCategorias(): array
    {
        $mapper = new Mapper($this->db, 'categorias_producto');
        $mapa = [];
        foreach ($mapper->find() as $categoria) {
            $mapa[(int) $categoria['id']] = (string) $categoria['nombre'];
        }
        return $mapa;
    }

    /**
     * Convierte los mappers en arrays listos para la vista.
     * La vista no calcula reglas de negocio.
     */
    private function prepararFilas(array $registros, array $categorias): array
    {
        $filas = [];

        foreach ($registros as $registro) {
            $fila = $registro->cast();
            $stock = (int) $fila['stock'];

            $filas[] = [
                'id'                => (int) $fila['id'],
                'nombre'            => (string) $fila['nombre'],
                'descripcion'       => (string) ($fila['descripcion'] ?? ''),
                'descripcion_corta' => (string) ($fila['descripcion_corta'] ?? ''),
                'precio'            => (float) $fila['precio'],
                'precio_formateado' => '$ ' . number_format((float) $fila['precio'], 0, ',', '.'),
                'stock'             => $stock,
                'stock_clase'       => $stock <= 0 ? 'product-card__stock--out' : ($stock <= 5 ? 'product-card__stock--low' : ''),
                'stock_texto'       => $stock <= 0 ? 'Sin stock' : ($stock <= 5 ? 'Reponer (' . $stock . ')' : 'Disponible'),
                'categoria'         => $categorias[(int) $fila['categoria_id']] ?? 'Sin categoría',
                'imagen'            => (string) $fila['imagen'],
            ];
        }

        return $filas;
    }
}
