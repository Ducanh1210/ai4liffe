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
  // To tránh Not Found khi chưa bật rewrite, luôn dùng index.php cho route nội bộ
  $hrefHome = $baseIndex . '/';
  $hrefStats = $baseIndex . '/stats';
  $hrefChat = $baseIndex . '/chat';
  ?>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <header class="site-header">
    <div class="container topbar">
      <a class="brand" href="<?= htmlspecialchars($hrefHome) ?>">
        <span class="brand-logo">FP</span>
        <span class="brand-text">FPOLY Advisor</span>
      </a>
      <nav class="nav">
        <a class="pill" href="<?= htmlspecialchars($hrefHome) ?>">Trang chủ</a>
        <a class="pill" href="<?= htmlspecialchars($hrefStats) ?>">Thống kê</a>
        <a class="pill" href="https://caodang.fpt.edu.vn" target="_blank" rel="noopener">FPT Polytechnic</a>
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