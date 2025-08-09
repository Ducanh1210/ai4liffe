<?php
namespace App\Controllers;

use App\Core\Controller;

class QrController extends Controller
{
    public function image(): void
    {
        $data = $_GET['data'] ?? '';
        $size = (int) ($_GET['s'] ?? 160);
        if ($size < 80 || $size > 512) {
            $size = 160;
        }
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);

        $png = $this->fetch($url);
        if ($png === null) {
            // 1x1 transparent PNG
            $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottQAAAABJRU5ErkJggg==');
        }
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=3600');
        echo $png;
    }

    private function fetch(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($resp !== false && $code >= 200 && $code < 300)
            return (string) $resp;
        return null;
    }
}


