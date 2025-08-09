<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class CurriculumModel
{
    public function byMajor(int $majorId): array
    {
        $stmt = Database::pdo()->prepare('SELECT semester, referenceId, code, name, credits FROM curriculum WHERE major_id = ? ORDER BY id');
        $stmt->execute([$majorId]);
        return $stmt->fetchAll();
    }
}


