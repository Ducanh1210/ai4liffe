<?php
namespace App\Services;

class AIService
{
    private string $provider;

    public function __construct()
    {
        $this->provider = env('AI_PROVIDER', 'mock'); // 'openai' | 'gemini' | 'mock'
    }

    public function recommendMajor(array $userInput, array $majorsContext): array
    {
        if ($this->provider === 'openai' && env('OPENAI_API_KEY')) {
            return $this->recommendWithOpenAI($userInput, $majorsContext);
        }
        if ($this->provider === 'gemini' && env('GEMINI_API_KEY')) {
            return $this->recommendWithGemini($userInput, $majorsContext);
        }
        return $this->recommendWithHeuristics($userInput, $majorsContext);
    }

    public function chat(array $messages, string $systemPrompt = '', array $domainContext = []): array
    {
        if ($this->provider === 'openai' && env('OPENAI_API_KEY')) {
            return $this->chatOpenAI($messages, $systemPrompt, $domainContext);
        }
        if ($this->provider === 'gemini' && env('GEMINI_API_KEY')) {
            return $this->chatGemini($messages, $systemPrompt, $domainContext);
        }
        return $this->chatHeuristic($messages, $systemPrompt, $domainContext);
    }

    private function recommendWithHeuristics(array $userInput, array $majorsContext): array
    {
        $text = strtolower(($userInput['interests'] ?? '') . ' ' . ($userInput['favorite_subjects'] ?? '') . ' ' . ($userInput['affinities'] ?? ''));
        $scores = [];
        foreach ($majorsContext as $m) {
            $score = 0;
            $keywords = implode(' ', $m['skills']);
            $kw = strtolower($keywords . ' ' . $m['name'] . ' ' . $m['code']);
            $overlap = array_intersect(array_filter(explode(' ', preg_replace('/[^a-zA-ZÀ-ỹ0-9 ]/u', ' ', $text))), array_filter(explode(' ', preg_replace('/[^a-zA-ZÀ-ỹ0-9 ]/u', ' ', $kw))));
            $score += count($overlap);
            if (str_contains($text, 'toán') || str_contains($text, 'logic')) {
                if (str_contains($kw, 'công nghệ') || str_contains($kw, 'lập trình')) $score += 3;
                if (str_contains($kw, 'cơ khí')) $score += 2;
            }
            if (str_contains($text, 'vẽ') || str_contains($text, 'thiết kế') || str_contains($text, 'đồ họa')) $score += str_contains($kw, 'thiết kế') ? 4 : 0;
            if (str_contains($text, 'marketing') || str_contains($text, 'truyền thông') || str_contains($text, 'quảng cáo')) $score += str_contains($kw, 'marketing') ? 4 : 0;
            $scores[$m['id']] = $score;
        }
        arsort($scores);
        $bestId = array_key_first($scores);
        $best = null;
        foreach ($majorsContext as $m) if ($m['id'] === $bestId) { $best = $m; break; }
        return [
            'provider' => 'heuristics',
            'recommended_major' => [
                'id' => $best['id'],
                'name' => $best['name'],
                'code' => $best['code'],
            ],
            'why' => 'Khớp từ khóa sở thích/kỹ năng với kỹ năng và mô tả ngành.',
            'top_alternatives' => $this->buildAlternatives($scores, $majorsContext, 3),
            'study_plan' => $best['curriculum'],
            'skills_to_focus' => array_slice($best['skills'], 0, 4),
        ];
    }

    private function buildAlternatives(array $scores, array $majorsContext, int $limit): array
    {
        $alts = [];
        $ranked = array_slice(array_keys($scores), 0, $limit + 1, true);
        foreach ($ranked as $id) {
            foreach ($majorsContext as $m) {
                if ($m['id'] === $id) {
                    $alts[] = [ 'id' => $m['id'], 'name' => $m['name'], 'code' => $m['code'] ];
                }
            }
        }
        return array_slice($alts, 1, $limit);
    }

    private function recommendWithOpenAI(array $userInput, array $majorsContext): array
    {
        $system = 'Bạn là cố vấn hướng nghiệp FPT Polytechnic. Dựa trên đầu vào học sinh và danh sách ngành (kèm kỹ năng, học phần), đề xuất ngành phù hợp duy nhất, kèm 2-3 lựa chọn thay thế, giải thích ngắn gọn, gợi ý kỹ năng cần tập trung và trích xuất kế hoạch học tập (curriculum) của ngành đã chọn. Trả về JSON với keys: provider,recommended_major{id,name,code},why,top_alternatives[{id,name,code}],skills_to_focus[...],study_plan[...].';
        $payload = [
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode([
                    'user' => $userInput,
                    'majors' => $majorsContext,
                ], JSON_UNESCAPED_UNICODE)],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2,
        ];
        $resp = $this->http('https://api.openai.com/v1/chat/completions', $payload, [
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type: application/json',
        ]);
        $json = json_decode($resp, true);
        $content = $json['choices'][0]['message']['content'] ?? '{}';
        $data = json_decode($content, true);
        if (!is_array($data)) { return $this->recommendWithHeuristics($userInput, $majorsContext); }
        $data['provider'] = 'openai';
        return $data;
    }

    private function recommendWithGemini(array $userInput, array $majorsContext): array
    {
        $prompt = 'Hãy vào vai cố vấn hướng nghiệp FPT Polytechnic...';
        $payload = [
            'contents' => [[
                'parts' => [[
                    'text' => $prompt . "\n\n" . json_encode([
                        'user' => $userInput,
                        'majors' => $majorsContext,
                    ], JSON_UNESCAPED_UNICODE),
                ]],
            ]],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json',
            ],
        ];
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode(env('GEMINI_API_KEY'));
        $resp = $this->http($url, $payload, ['Content-Type: application/json']);
        $json = json_decode($resp, true);
        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $data = json_decode($content, true);
        if (!is_array($data)) { return $this->recommendWithHeuristics($userInput, $majorsContext); }
        $data['provider'] = 'gemini';
        return $data;
    }

    private function http(string $url, array $json, array $headers): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 30,
        ]);
        $resp = curl_exec($ch);
        if ($resp === false) {
            return '{}';
        }
        curl_close($ch);
        return (string)$resp;
    }

    public function chatStreamOpenAI(array $messages, string $systemPrompt, array $domainContext, callable $onEvent): void
    {
        if (!env('OPENAI_API_KEY')) {
            // Fallback to non-stream
            $result = $this->chatOpenAI($messages, $systemPrompt, ['majors' => $domainContext]);
            foreach (str_split($result['messages'][count($result['messages']) - 1]['content'] ?? '') as $ch) {
                $onEvent('token', $ch);
            }
            $onEvent('done', null);
            return;
        }
        $payload = [
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'stream' => true,
            'messages' => array_values(array_filter([
                $systemPrompt ? ['role' => 'system', 'content' => $systemPrompt] : null,
                ['role' => 'system', 'content' => 'Dữ liệu nội bộ: ' . json_encode(['majors' => $domainContext], JSON_UNESCAPED_UNICODE)],
                ...$messages,
            ])),
            'temperature' => 0.2,
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type: application/json',
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_WRITEFUNCTION => function ($ch, $data) use ($onEvent) {
                $lines = explode("\n", trim($data));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || !str_starts_with($line, 'data:')) continue;
                    $json = trim(substr($line, 5));
                    if ($json === '[DONE]') { $onEvent('done', null); continue; }
                    $chunk = json_decode($json, true);
                    $delta = $chunk['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') { $onEvent('token', $delta); }
                }
                return strlen($data);
            },
            CURLOPT_RETURNTRANSFER => false,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function chatHeuristic(array $messages, string $systemPrompt, array $domainContext): array
    {
        $lastUser = '';
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            if (($messages[$i]['role'] ?? '') === 'user') { $lastUser = (string)$messages[$i]['content']; break; }
        }
        $text = trim($lastUser);
        $reply = "Mình là trợ lý tư vấn FPT Polytechnic. \n\n";
        if ($text === '') {
            $reply .= "## Tóm tắt\nBạn có thể hỏi về ngành học, chương trình đào tạo, tuyển sinh hoặc cơ hội nghề nghiệp.\n\n## Gợi ý\nHãy mô tả mục tiêu, sở thích, môn mạnh/yếu để mình tư vấn sâu hơn.";
        } elseif (stripos($text, 'học phí') !== false) {
            $reply .= "## Tóm tắt\nHọc phí thay đổi theo kỳ/khóa.\n\n## Hành động\nTham khảo trang tuyển sinh FPT Polytechnic hoặc liên hệ hotline để có thông tin cập nhật.";
        } elseif (stripos($text, 'ngành') !== false) {
            $majors = array_map(fn($m) => $m['name'], $domainContext['majors'] ?? []);
            $reply .= "## Tóm tắt\nCác ngành tiêu biểu: " . implode(', ', $majors) . ".\n\n## Gợi ý\nBạn quan tâm mảng nào để mình phân tích sâu hơn theo kỹ năng và lộ trình học?";
        } else {
            $reply .= "## Tóm tắt\nMình sẽ hỗ trợ phân tích theo câu hỏi của bạn.\n\n## Gợi ý\nBạn có thể nêu rõ mục tiêu, sở thích, kỹ năng, môn học yêu thích để nhận tư vấn chi tiết theo chương trình của trường.";
        }
        return [
            'provider' => 'heuristics',
            'messages' => array_merge($messages, [['role' => 'assistant', 'content' => $reply]]),
        ];
    }

    private function chatOpenAI(array $messages, string $systemPrompt, array $domainContext): array
    {
        $payload = [
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'messages' => array_values(array_filter([
                $systemPrompt ? ['role' => 'system', 'content' => $systemPrompt] : null,
                ['role' => 'system', 'content' => 'Dữ liệu nội bộ: ' . json_encode($domainContext, JSON_UNESCAPED_UNICODE)],
                ...$messages,
            ])),
            'temperature' => 0.2,
        ];
        $resp = $this->http('https://api.openai.com/v1/chat/completions', $payload, [
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type: application/json',
        ]);
        $json = json_decode($resp, true);
        $assistant = $json['choices'][0]['message'] ?? ['role' => 'assistant', 'content' => ''];
        return [
            'provider' => 'openai',
            'messages' => array_merge($messages, [$assistant]),
        ];
    }

    private function chatGemini(array $messages, string $systemPrompt, array $domainContext): array
    {
        $parts = [];
        if ($systemPrompt) { $parts[] = ['text' => '[SYSTEM]\n' . $systemPrompt]; }
        $parts[] = ['text' => '[SYSTEM]\nDữ liệu nội bộ: ' . json_encode($domainContext, JSON_UNESCAPED_UNICODE)];
        foreach ($messages as $m) { $parts[] = ['text' => '[' . strtoupper($m['role']) . ']\n' . $m['content']]; }
        $payload = [
            'contents' => [[ 'parts' => $parts ]],
            'generationConfig' => [ 'temperature' => 0.2 ],
        ];
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode(env('GEMINI_API_KEY'));
        $resp = $this->http($url, $payload, ['Content-Type: application/json']);
        $json = json_decode($resp, true);
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        return [
            'provider' => 'gemini',
            'messages' => array_merge($messages, [['role' => 'assistant', 'content' => $text]]),
        ];
    }
}


