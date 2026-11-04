<?php
/**
 * app/Models/Producto.php · Modelo del catálogo de repuestos (TP10).
 * Todas las consultas usan PDO con sentencias preparadas.
 */

declare(strict_types=1);

namespace App\Models;

class Producto extends Modelo
{
    /** Devuelve las categorías con la cantidad de productos activos. */
    public function categorias(): array
    {
        return $this->ejecutar(
            "SELECT c.id, c.nombre, c.slug, COUNT(p.id) AS total
             FROM categorias_producto c
             LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1
             GROUP BY c.id, c.nombre, c.slug
             ORDER BY c.id"
        )->fetchAll();
    }

    /**
     * Busca productos activos por texto y/o categoría.
     * Los parámetros se enlazan, nunca se concatenan.
     */
    public function buscar(string $texto = '', string $categoriaSlug = ''): array
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.descripcion_corta,
                       p.precio, p.stock, p.imagen,
                       c.nombre AS categoria, c.slug AS categoria_slug
                FROM productos p
                INNER JOIN categorias_producto c ON c.id = p.categoria_id
                WHERE p.activo = 1";
        $parametros = [];

        if ($texto !== '') {
            $sql .= " AND (p.nombre LIKE :texto OR p.descripcion LIKE :texto OR c.nombre LIKE :texto)";
            $parametros[':texto'] = '%' . $texto . '%';
        }

        if ($categoriaSlug !== '') {
            $sql .= " AND c.slug = :categoria";
            $parametros[':categoria'] = $categoriaSlug;
        }

        $sql .= " ORDER BY p.stock > 0 DESC, p.nombre";

        return $this->ejecutar($sql, $parametros)->fetchAll();
    }

    /** Busca un producto activo por id. Devuelve null si no existe. */
    public function encontrar(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $producto = $this->ejecutar(
            "SELECT p.*, c.nombre AS categoria, c.slug AS categoria_slug
             FROM productos p
             INNER JOIN categorias_producto c ON c.id = p.categoria_id
             WHERE p.id = :id AND p.activo = 1
             LIMIT 1",
            [':id' => $id]
        )->fetch();

        return $producto ?: null;
    }

    /** Cantidad total de productos activos. */
    public function total(): int
    {
        return (int) $this->ejecutar("SELECT COUNT(*) FROM productos WHERE activo = 1")->fetchColumn();
    }
}
