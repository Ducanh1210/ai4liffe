<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AIService;
use App\Models\MajorModel;
use App\Models\SkillModel;
use App\Models\CurriculumModel;

class ChatController extends Controller
{
    public function index(): void
    {
        $this->view('chat');
    }

    public function api(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $messages = $input['messages'] ?? [];
        $mode = $input['mode'] ?? 'advisor'; // 'advisor' | 'general'
        $context = [];
        if ($mode === 'advisor') {
            // Load domain context from DB
            $majorModel = new MajorModel();
            $skillModel = new SkillModel();
            $currModel = new CurriculumModel();
            $majors = $majorModel->all();
            foreach ($majors as $m) {
                $context[] = [
                    'id' => (int) $m['id'],
                    'name' => $m['name'],
                    'code' => $m['code'],
                    'skills' => $skillModel->byMajor((int) $m['id']),
                    'curriculum_preview' => array_slice($currModel->byMajor((int) $m['id']), 0, 6),
                ];
            }
        }
        // System prompt based on mode
        $system = $mode === 'general'
            ? 'Bạn là trợ lý AI tổng quát. Hãy trả lời chính xác, khoa học, có cấu trúc, kèm ví dụ, công thức/bước làm khi phù hợp. Ưu tiên tiếng Việt và Markdown.'
            : 'Bạn là trợ lý AI của FPT Polytechnic. Hãy trả lời KHOA HỌC, CHI TIẾT, và CÓ CẤU TRÚC bằng Markdown với các phần: \n' .
            '## Tóm tắt\n## Phân tích khoa học\n## Gợi ý ngành phù hợp (nêu 1-3 ngành từ danh sách nội bộ)\n## Vì sao phù hợp\n## Kế hoạch hành động (bước tiếp theo)\n' .
            'Nếu câu hỏi ngoài phạm vi, phản hồi lịch sự và đề nghị liên hệ bộ phận tuyển sinh. Ngôn ngữ: ưu tiên tiếng Việt.';
        $ai = new AIService();

        // If advisor mode and user is asking for major recommendation, produce a structured recommendation
        $lastUser = '';
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if (($messages[$i]['role'] ?? '') === 'user') {
                $lastUser = (string) $messages[$i]['content'];
                break;
            }
        }

        $shouldRecommend = $mode === 'advisor' && (
            preg_match('/(chọn|học|ngành|tư\s*vấn)/iu', $lastUser) === 1
        );

        if ($shouldRecommend && !empty($context)) {
            // Build majorsContext with fuller curriculum for better output
            $majorsContext = $context;
            // Heuristic recommendation (works offline); if OpenAI/Gemini enabled, AIService will internally use it
            $rec = $ai->recommendMajor([
                'name' => '',
                'age' => 0,
                'interests' => $lastUser,
                'strengths' => $lastUser,
                'favorite_subjects' => $lastUser,
                'scores' => '',
                'career_orientation' => '',
                'learning_style' => '',
                'affinities' => $lastUser,
            ], $majorsContext);

            $md = "## Tóm tắt\n" .
                "Đề xuất chính: **" . ($rec['recommended_major']['name'] ?? 'N/A') . "** (" . ($rec['recommended_major']['code'] ?? '') . ")\n\n" .
                "## Phân tích khoa học\nKhớp từ khóa trong câu hỏi của bạn với kỹ năng/ngữ nghĩa ngành để chấm điểm phù hợp.\n\n" .
                "## Gợi ý ngành phù hợp\n- " . ($rec['recommended_major']['name'] ?? '') . "\n";
            if (!empty($rec['top_alternatives'])) {
                foreach ($rec['top_alternatives'] as $alt) {
                    $md .= "- " . ($alt['name'] ?? '') . "\n";
                }
            }
            $md .= "\n## Vì sao phù hợp\n" . ($rec['why'] ?? '') . "\n\n";
            if (!empty($rec['skills_to_focus'])) {
                $md .= "## Kỹ năng nên tập trung\n";
                foreach ($rec['skills_to_focus'] as $s) {
                    $md .= "- $s\n";
                }
                $md .= "\n";
            }
            if (!empty($rec['study_plan'])) {
                $md .= "## Kế hoạch học tập tham khảo\n";
                $chunks = array_slice($rec['study_plan'], 0, 8);
                foreach ($chunks as $c) {
                    $md .= "- [" . ($c['semester'] ?? '') . "] " . ($c['code'] ?? '') . ": " . ($c['name'] ?? '') . " (" . (int) ($c['credits'] ?? 0) . " tín)\n";
                }
                $md .= "\n";
            }
            $md .= "## Kế hoạch hành động\n1) Xác định rõ mục tiêu nghề nghiệp 2) Bổ sung kỹ năng còn thiếu 3) Tham khảo chương trình chi tiết và liên hệ tuyển sinh.";

            $answer = [
                'provider' => $rec['provider'] ?? 'heuristics',
                'messages' => array_merge($messages, [['role' => 'assistant', 'content' => $md]]),
            ];
        } else {
            $answer = $ai->chat($messages, $system, $mode === 'advisor' ? ['majors' => $context] : []);
        }
        $this->json(['answer' => $answer]);
    }

    public function stream(): void
    {
        // Requires OpenAI; fallback to non-stream if unavailable
        if (env('AI_PROVIDER') !== 'openai' || !env('OPENAI_API_KEY')) {
            $this->api();
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $messages = $input['messages'] ?? [];
        $mode = $input['mode'] ?? 'advisor';

        $system = $mode === 'general'
            ? 'Bạn là trợ lý AI tổng quát. Hãy trả lời chính xác, khoa học, có cấu trúc, kèm ví dụ, công thức/bước làm khi phù hợp. Ưu tiên tiếng Việt và Markdown.'
            : 'Bạn là trợ lý AI của FPT Polytechnic. Hãy trả lời KHOA HỌC, CHI TIẾT, và CÓ CẤU TRÚC bằng Markdown.';

        // Domain context for advisor mode
        $domain = [];
        if ($mode === 'advisor') {
            $majors = (new MajorModel())->all();
            $skills = new SkillModel();
            $curr = new CurriculumModel();
            foreach ($majors as $m) {
                $domain[] = [
                    'id' => (int)$m['id'], 'name' => $m['name'], 'code' => $m['code'],
                    'skills' => $skills->byMajor((int)$m['id']),
                    'curriculum_preview' => array_slice($curr->byMajor((int)$m['id']), 0, 6),
                ];
            }
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        $ai = new AIService();
        $ai->chatStreamOpenAI($messages, $system, $domain, function ($event, $data) {
            if ($event === 'token') {
                echo 'data: ' . json_encode(['type' => 'token', 'content' => $data]) . "\n\n";
            } elseif ($event === 'done') {
                echo 'data: ' . json_encode(['type' => 'done']) . "\n\n";
            }
            @ob_flush(); flush();
        });
    }
}


