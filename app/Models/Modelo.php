<?php
/**
 * app/Models/Modelo.php · Modelo base del MVC nativo (TP10).
 * Concentra la conexión PDO y utilidades comunes a los modelos.
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

abstract class Modelo
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Ejecuta una consulta preparada y devuelve el statement. */
    protected function ejecutar(string $sql, array $parametros = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt;
    }
}
