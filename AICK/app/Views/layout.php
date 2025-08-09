<?php
?><!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FPT Polytechnic - Tư vấn chọn ngành</title>
  <?php
  $baseUrl = $baseUrl ?? '';
  $baseIndex = $baseIndex ?? $baseUrl . '/index.php';
  $hasRewrite = (strpos($_SERVER['REQUEST_URI'] ?? '', '/index.php') === false);
  $hrefHome = $hasRewrite ? ($baseUrl . '/') : ($baseIndex . '/');
  $hrefStats = $hasRewrite ? ($baseUrl . '/stats') : ($baseIndex . '/stats');
  $hrefChat = $hasRewrite ? ($baseUrl . '/chat') : ($baseIndex . '/chat');
  ?>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <header class="site-header">
    <div class="container">
      <a class="brand" href="<?= htmlspecialchars($hrefHome) ?>">FPOLY Advisor</a>
      <nav>
        <a href="<?= htmlspecialchars($hrefHome) ?>">Trang chủ</a>
        <a href="<?= htmlspecialchars($hrefStats) ?>">Thống kê</a>
        <a href="<?= htmlspecialchars($hrefChat) ?>">Chat</a>
        <a href="https://caodang.fpt.edu.vn" target="_blank" rel="noopener">FPT Polytechnic</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <?= $content ?>
  </main>
  <footer class="site-footer">
    <div class="container">&copy; <?= date('Y') ?> FPT Polytechnic — Built for Hackathon</div>
  </footer>
  <script src="<?= $baseUrl ?>/public/js/app.js"></script>
</body>
<?php // end layout ?>