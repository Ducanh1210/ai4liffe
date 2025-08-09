<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\MajorModel;
use App\Models\CurriculumModel;
use App\Models\SkillModel;
use App\Models\AssessmentModel;
use App\Services\AIService;

class ResultController extends Controller
{
    public function recommend(): void
    {
        $input = [
            'name' => trim($_POST['name'] ?? ''),
            'age' => (int) ($_POST['age'] ?? 0),
            'interests' => trim($_POST['interests'] ?? ''),
            'strengths' => trim($_POST['strengths'] ?? ''),
            'favorite_subjects' => trim($_POST['favorite_subjects'] ?? ''),
            'scores' => trim($_POST['scores'] ?? ''),
            'career_orientation' => trim($_POST['career_orientation'] ?? ''),
            'learning_style' => trim($_POST['learning_style'] ?? ''),
            'affinities' => trim($_POST['affinities'] ?? ''),
        ];

        $majorModel = new MajorModel();
        $curriculumModel = new CurriculumModel();
        $skillModel = new SkillModel();
        $assessmentModel = new AssessmentModel();

        $majors = $majorModel->all();
        $majorsContext = [];
        foreach ($majors as $major) {
            $majorsContext[] = [
                'id' => $major['id'],
                'name' => $major['name'],
                'code' => $major['code'],
                'skills' => $skillModel->byMajor((int) $major['id']),
                'curriculum' => $curriculumModel->byMajor((int) $major['id']),
            ];
        }

        $ai = new AIService();
        $aiResult = $ai->recommendMajor($input, $majorsContext);

        // Persist assessment
        $assessmentId = $assessmentModel->create([
            'student_name' => $input['name'],
            'input_json' => json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ai_result_json' => json_encode($aiResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseUrl = rtrim(dirname($scriptName), '/');
        if ($baseUrl === '/') {
            $baseUrl = '';
        }
        $baseIndex = rtrim($scriptName, '/');
        // Prefer pretty URL; fallback to index.php route when rewrite off
        $target = ($this->hasRewrite() ? ($baseUrl . '/result/' . $assessmentId) : ($baseIndex . '/result/' . $assessmentId));
        header('Location: ' . $target);
        exit;
    }

    public function show(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $assessmentModel = new AssessmentModel();
        $record = $assessmentModel->find($id);
        if (!$record) {
            http_response_code(404);
            echo 'Không tìm thấy kết quả';
            return;
        }

        $ai = json_decode($record['ai_result_json'], true) ?? [];

        $this->view('result', [
            'record' => $record,
            'ai' => $ai,
        ]);
    }

    private function hasRewrite(): bool
    {
        // Heuristic: if REQUEST_URI contains '/index.php', assume no rewrite
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, '/index.php') === false;
    }
}


