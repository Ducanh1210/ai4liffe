<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class SkillModel
{
    public function byMajor(int $majorId): array
    {
        $stmt = Database::pdo()->prepare('SELECT skill FROM skills WHERE major_id = ? ORDER BY id');
        $stmt->execute([$majorId]);
        return array_map(fn($r) => $r['skill'], $stmt->fetchAll());
    }
}


