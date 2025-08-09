<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AssessmentModel
{
    public function ensureTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS assessments (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_name VARCHAR(255) NOT NULL,
            input_json JSON NOT NULL,
            ai_result_json JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        Database::pdo()->exec($sql);
    }

    public function create(array $data): int
    {
        $stmt = Database::pdo()->prepare('INSERT INTO assessments (student_name, input_json, ai_result_json) VALUES (?, ?, ?)');
        $stmt->execute([
            $data['student_name'] ?? '',
            $data['input_json'] ?? '{}',
            $data['ai_result_json'] ?? '{}',
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM assessments WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function summaryByMajor(): array
    {
        // ai_result_json expected: { recommended_major: { id, name, code }, ... }
        $sql = 'SELECT 
            JSON_UNQUOTE(JSON_EXTRACT(ai_result_json, "$.recommended_major.name")) AS major_name,
            COUNT(*) AS total
        FROM assessments
        GROUP BY major_name
        ORDER BY total DESC';
        $stmt = Database::pdo()->query($sql);
        return array_values(array_filter($stmt->fetchAll(), fn($r) => $r['major_name'] !== null));
    }
}


