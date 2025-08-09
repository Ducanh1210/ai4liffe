<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class MajorModel
{
    public function all(): array
    {
        $stmt = Database::pdo()->query('SELECT id, name, code FROM majors ORDER BY id');
        return $stmt->fetchAll();
    }
}


