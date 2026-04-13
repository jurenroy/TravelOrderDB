<?php
namespace App\Services;

use PDO;
use PDOException;

class AccessService
{
    protected $pdo;

    public function __construct()
    {
        // Get DSN from .env
        $dsn = env('DB_ACCESS_DSN');

        try {
            $this->pdo = new PDO($dsn);
        } catch (PDOException $e) {
            throw new \Exception("Access DB connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
