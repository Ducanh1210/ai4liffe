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
        $baseIndex = rtrim($scriptName, '/');
        // Luôn dùng index.php để tránh phụ thuộc rewrite
        $target = $baseIndex . '/result/' . $assessmentId;
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
        $rec = $ai['recommended_major'] ?? null;

        // Build focus courses list from DB for recommended major (early semesters)
        $focusCourses = [];
        $skillsImprove = [];
        $input = json_decode($record['input_json'] ?? '{}', true) ?? [];
        $studentText = strtolower(trim(($input['interests'] ?? '') . ' ' . ($input['strengths'] ?? '') . ' ' . ($input['favorite_subjects'] ?? '') . ' ' . ($input['career_orientation'] ?? '') . ' ' . ($input['affinities'] ?? '')));
        if ($rec) {
            $currModel = new CurriculumModel();
            $majorModel = new MajorModel();
            $majorId = (int) ($rec['id'] ?? 0);
            if ($majorId <= 0 && !empty($rec['code'])) {
                // Try to resolve by code
                foreach ($majorModel->all() as $m) {
                    if (strcasecmp($m['code'], $rec['code']) === 0) {
                        $majorId = (int) $m['id'];
                        break;
                    }
                }
            }
            if ($majorId > 0) {
                $curr = $currModel->byMajor($majorId);
                // Prefer specialized semesters first (ky3, ky4, ky5), then fill with earlier ones
                $spec = array_values(array_filter($curr, fn($c) => in_array(strtolower($c['semester']), ['ky3', 'ky4', 'ky5'])));
                $nonSpec = array_values(array_filter($curr, fn($c) => in_array(strtolower($c['semester']), ['ky1', 'ky2'])));
                $ordered = array_merge($spec, $nonSpec);
                // Avoid duplicates just in case
                $seen = [];
                $focusCourses = [];
                foreach ($ordered as $c) {
                    $key = ($c['code'] ?? '') . '|' . ($c['name'] ?? '');
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $focusCourses[] = $c;
                    }
                    if (count($focusCourses) >= 5)
                        break;
                }
                // Compute skills to improve from major skills list
                $skillModel = new SkillModel();
                $skills = $skillModel->byMajor($majorId);
                $scores = [];
                foreach ($skills as $sk) {
                    $tokens = array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($sk, 'UTF-8')), fn($t) => mb_strlen($t, 'UTF-8') >= 4));
                    $hit = 0;
                    foreach ($tokens as $t) {
                        if ($t !== '' && strpos($studentText, $t) !== false) {
                            $hit++;
                        }
                    }
                    $scores[] = ['skill' => $sk, 'match' => $hit];
                }
                usort($scores, fn($a, $b) => $a['match'] <=> $b['match']);
                $skillsImprove = array_map(fn($x) => $x['skill'], array_slice($scores, 0, 5));
            }
        }

        $this->view('result', [
            'record' => $record,
            'ai' => $ai,
            'focusCourses' => $focusCourses,
            'skillsImprove' => $skillsImprove,
        ]);
    }

    private function hasRewrite(): bool
    {
        return false;
    }
}


