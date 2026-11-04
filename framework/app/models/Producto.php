<?php
/**
 * framework/app/models/Producto.php · Modelo con el ORM de Fat-Free (TP10 B).
 *
 * Extiende DB\SQL\Mapper: cada fila de la tabla `productos` se mapea a un
 * objeto, y las búsquedas usan los filtros del ORM con parámetros enlazados.
 */

declare(strict_types=1);

namespace App\Models;

use DB\SQL;
use DB\SQL\Mapper;

class Producto extends Mapper
{
    public function __construct(SQL $db)
    {
        parent::__construct($db, 'productos');
    }

    /** Productos activos ordenados por stock (primero los que requieren reposición). */
    public function activos(): array
    {
        return $this->find(
            ['activo = ?', 1],
            ['order' => 'stock ASC, nombre ASC']
        );
    }

    /** Búsqueda por nombre o descripción usando el filtro del ORM. */
    public function buscar(string $texto): array
    {
        $like = '%' . $texto . '%';

        return $this->find(
            ['activo = ? AND (nombre LIKE ? OR descripcion LIKE ?)', 1, $like, $like],
            ['order' => 'stock ASC, nombre ASC']
        );
    }

    /** Carga un producto activo por id. Devuelve false si no existe. */
    public function activoPorId(int $id): self|false
    {
        return $this->load(['id = ? AND activo = ?', $id, 1]);
    }

    /** Cantidad de productos activos. */
    public function totalActivos(): int
    {
        return $this->count(['activo = ?', 1]);
    }
}
