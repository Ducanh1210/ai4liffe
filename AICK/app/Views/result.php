<?php
$ai = $ai ?? [];
$rec = $ai['recommended_major'] ?? null;
?>
<section class="card">
  <div class="result-header">
    <div>
      <h2>Kết quả tư vấn</h2>
      <?php if ($rec): ?>
        <p>Ngành phù hợp: <strong><?= htmlspecialchars($rec['name'] ?? '') ?></strong> (<?= htmlspecialchars($rec['code'] ?? '') ?>)</p>
      <?php endif; ?>
      <p class="muted">Mã kết quả: #<?= (int)$record['id'] ?></p>
    </div>
    <div class="qr" id="qr"></div>
  </div>

  <?php if (!empty($ai['why'])): ?>
    <p><strong>Vì sao phù hợp:</strong> <?= htmlspecialchars($ai['why']) ?></p>
  <?php endif; ?>

  <?php if (!empty($ai['skills_to_focus'])): ?>
    <h3>Kỹ năng nên tập trung</h3>
    <ul>
      <?php foreach ($ai['skills_to_focus'] as $s): ?>
        <li><?= htmlspecialchars($s) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (!empty($focusCourses)): ?>
    <h3>Môn học nên ưu tiên (gợi ý)</h3>
    <ul>
      <?php foreach ($focusCourses as $fc): ?>
        <li>[<?= htmlspecialchars($fc['semester']) ?>] <?= htmlspecialchars($fc['code']) ?> - <?= htmlspecialchars($fc['name']) ?> (<?= (int)$fc['credits'] ?> tín)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (!empty($ai['study_plan'])): ?>
    <h3>Kế hoạch học tập tham khảo</h3>
    <div class="table">
      <div class="table-row table-head">
        <div>Học kỳ</div><div>Mã HP</div><div>Tên học phần</div><div>Tín chỉ</div>
      </div>
      <?php foreach ($ai['study_plan'] as $c): ?>
        <div class="table-row">
          <div><?= htmlspecialchars($c['semester'] ?? '') ?></div>
          <div><?= htmlspecialchars($c['code'] ?? '') ?></div>
          <div><?= htmlspecialchars($c['name'] ?? '') ?></div>
          <div><?= (int)($c['credits'] ?? 0) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($ai['top_alternatives'])): ?>
    <h3>Lựa chọn thay thế</h3>
    <div class="chips">
      <?php foreach ($ai['top_alternatives'] as $alt): ?>
        <span class="chip" title="<?= htmlspecialchars($alt['code']) ?>"><?= htmlspecialchars($alt['name']) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php $baseUrl = $baseUrl ?? ''; $baseIndex = $baseIndex ?? $baseUrl.'/index.php'; $hasRewrite = (strpos($_SERVER['REQUEST_URI'] ?? '', '/index.php') === false); $homeHref = $hasRewrite ? ($baseUrl.'/') : ($baseIndex.'/'); ?>
  <div class="actions">
    <a class="btn" href="<?= htmlspecialchars($homeHref) ?>">Tạo tư vấn mới</a>
    <button class="btn" id="btn-copy">Sao chép link</button>
    <button class="btn" id="btn-qr">Tải QR</button>
    <button class="btn btn-primary" id="btn-download">Tải infographic</button>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
  const url = window.location.href;
  const qrCanvas = document.createElement('canvas');
  QRCode.toCanvas(qrCanvas, url, { width: 96 });
  document.getElementById('qr').appendChild(qrCanvas);
  document.getElementById('btn-download').addEventListener('click', async () => {
    const el = document.querySelector('.card');
    const blob = await htmlToImage(el);
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'fpoly-advisor-<?= (int)$record['id'] ?>.png';
    a.click();
  });
  document.getElementById('btn-copy').addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(url); alert('Đã sao chép liên kết'); } catch(e) { prompt('Sao chép liên kết:', url); }
  });
  document.getElementById('btn-qr').addEventListener('click', () => {
    const a = document.createElement('a');
    a.href = qrCanvas.toDataURL('image/png');
    a.download = 'qr-<?= (int)$record['id'] ?>.png';
    a.click();
  });
</script>


