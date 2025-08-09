<?php
$ai = $ai ?? [];
$rec = $ai['recommended_major'] ?? null;
?>

<!-- Modern Header Section -->
<div class="result-hero">
  <div class="hero-background"></div>
  <div class="hero-content">
    <div class="hero-badge">
      <i class="icon-check"></i>
      <span>Kết quả tư vấn</span>
    </div>
    <h1 class="hero-title">
      <?php if ($rec): ?>
        <p>Ngành phù hợp: <strong><?= htmlspecialchars($rec['name'] ?? '') ?></strong>
          (<?= htmlspecialchars($rec['code'] ?? '') ?>)</p>
      <?php endif; ?>
    </h1>
    <?php if ($rec): ?>
      <p class="hero-subtitle">
        Mã ngành: <span class="highlight"><?= htmlspecialchars($rec['code'] ?? '') ?></span>
      </p>
    <?php endif; ?>
    <div class="hero-meta">
      <span class="meta-item">
        <i class="icon-hash"></i>
        Mã kết quả: #<?= (int) $record['id'] ?>
      </span>
      <span class="meta-item">
        <i class="icon-clock"></i>
        <?= date('d/m/Y H:i', strtotime($record['created_at'] ?? 'now')) ?>
      </span>
    </div>
  </div>

  <!-- QR Code Card -->
  <div class="qr-card">
    <div class="qr-header">
      <h3>Chia sẻ kết quả</h3>
      <p>Quét mã QR để xem trên thiết bị khác</p>
    </div>
    <div class="qr-container" id="qr"></div>
    <button class="qr-share-btn" id="btn-copy">
      <i class="icon-share"></i>
      Sao chép link
    </button>
  </div>
</div>

<!-- Main Content Grid -->
<div class="result-grid">

  <!-- Why Section -->
  <?php if (!empty($ai['why'])): ?>
    <div class="result-card why-card">
      <div class="card-header">
        <div class="card-icon">
          <i class="icon-lightbulb"></i>
        </div>
        <h3>Tại sao phù hợp với bạn?</h3>
      </div>
      <div class="card-content">
        <p class="why-text"><?= htmlspecialchars($ai['why']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Skills Section -->
  <?php if (!empty($ai['skills_to_focus'])): ?>
    <div class="result-card skills-card">
      <div class="card-header">
        <div class="card-icon">
          <i class="icon-target"></i>
        </div>
        <h3>Kỹ năng cần phát triển</h3>
      </div>
      <div class="card-content">
        <div class="skills-grid">
          <?php foreach ($ai['skills_to_focus'] as $s): ?>
            <div class="skill-tag">
              <i class="icon-check-small"></i>
              <span><?= htmlspecialchars($s) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Priority Courses -->
  <?php if (!empty($focusCourses)): ?>
    <div class="result-card courses-card">
      <div class="card-header">
        <div class="card-icon">
          <i class="icon-star"></i>
        </div>
        <h3>Môn học ưu tiên</h3>
      </div>
      <div class="card-content">
        <div class="courses-list">
          <?php foreach ($focusCourses as $fc): ?>
            <div class="course-item">
              <div class="course-semester"><?= htmlspecialchars($fc['semester']) ?></div>
              <div class="course-info">
                <h4><?= htmlspecialchars($fc['name']) ?></h4>
                <span class="course-code"><?= htmlspecialchars($fc['code']) ?></span>
              </div>
              <div class="course-credits"><?= (int) $fc['credits'] ?> tín</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Study Plan -->
  <?php if (!empty($ai['study_plan'])): ?>
    <div class="result-card study-plan-card full-width">
      <div class="card-header">
        <div class="card-icon">
          <i class="icon-calendar"></i>
        </div>
        <h3>Kế hoạch học tập chi tiết</h3>
      </div>
      <div class="card-content">
        <div class="study-plan-table">
          <div class="table-header">
            <div class="col-semester">Học kỳ</div>
            <div class="col-code">Mã HP</div>
            <div class="col-name">Tên học phần</div>
            <div class="col-credits">Tín chỉ</div>
          </div>
          <div class="table-body">
            <?php foreach ($ai['study_plan'] as $c): ?>
              <div class="table-row">
                <div class="col-semester">
                  <span class="semester-badge"><?= htmlspecialchars($c['semester'] ?? '') ?></span>
                </div>
                <div class="col-code"><?= htmlspecialchars($c['code'] ?? '') ?></div>
                <div class="col-name"><?= htmlspecialchars($c['name'] ?? '') ?></div>
                <div class="col-credits">
                  <span class="credits-badge"><?= (int) ($c['credits'] ?? 0) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Alternatives -->
  <?php if (!empty($ai['top_alternatives'])): ?>
    <div class="result-card alternatives-card">
      <div class="card-header">
        <div class="card-icon">
          <i class="icon-layers"></i>
        </div>
        <h3>Lựa chọn thay thế</h3>
      </div>
      <div class="card-content">
        <div class="alternatives-grid">
          <?php foreach ($ai['top_alternatives'] as $alt): ?>
            <div class="alternative-item">
              <div class="alt-name"><?= htmlspecialchars($alt['name']) ?></div>
              <div class="alt-code"><?= htmlspecialchars($alt['code']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<!-- Action Section -->
<?php $baseUrl = $baseUrl ?? '';
$baseIndex = $baseIndex ?? $baseUrl . '/index.php';
$hasRewrite = (strpos($_SERVER['REQUEST_URI'] ?? '', '/index.php') === false);
$homeHref = $hasRewrite ? ($baseUrl . '/') : ($baseIndex . '/'); ?>

<div class="action-section">
  <div class="action-card">
    <h3>Tiếp theo bạn muốn làm gì?</h3>
    <div class="action-buttons">
      <a class="action-btn primary" href="<?= htmlspecialchars($homeHref) ?>">
        <i class="icon-plus"></i>
        <span>Tạo tư vấn mới</span>
      </a>
      <button class="action-btn secondary" id="btn-download">
        <i class="icon-download"></i>
        <span>Tải infographic</span>
      </button>
      <button class="action-btn secondary" id="btn-share">
        <i class="icon-share-2"></i>
        <span>Chia sẻ kết quả</span>
      </button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
  // Initialize page
  document.addEventListener('DOMContentLoaded', function () {
    initializeQR();
    initializeActions();
    addScrollAnimations();
  });

  function initializeQR() {
    const url = window.location.href;
    const qrContainer = document.getElementById('qr');

    try {
      if (window.QRCode && QRCode.toCanvas) {
        const qrCanvas = document.createElement('canvas');
        QRCode.toCanvas(qrCanvas, url, {
          width: 140,
          margin: 2,
          color: {
            dark: '#1f2937',
            light: '#ffffff'
          }
        });
        qrContainer.appendChild(qrCanvas);
      } else {
        throw new Error('lib-missing');
      }
    } catch (e) {
      const img = new Image();
      img.alt = 'QR Code';
      img.className = 'qr-fallback';
      img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(url);
      qrContainer.appendChild(img);
    }
  }

  function initializeActions() {
    const url = window.location.href;

    // Copy link functionality
    document.getElementById('btn-copy').addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(url);
        showNotification('Đã sao chép link thành công!', 'success');
      } catch (e) {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showNotification('Đã sao chép link!', 'success');
      }
    });

    // Download functionality
    document.getElementById('btn-download').addEventListener('click', async () => {
      try {
        const element = document.querySelector('.result-hero, .result-grid');
        if (window.htmlToImage) {
          const blob = await htmlToImage(element);
          if (blob) {
            window.downloadBlob(blob, 'fpoly-advisor-<?= (int) $record['id'] ?>.png');
            showNotification('Đang tải xuống...', 'info');
          } else {
            throw new Error('Không thể tạo ảnh');
          }
        } else {
          throw new Error('Chức năng tải xuống chưa sẵn sàng');
        }
      } catch (e) {
        showNotification('Không thể tải xuống. Vui lòng thử lại.', 'error');
        console.error(e);
      }
    });

    // Share functionality
    document.getElementById('btn-share').addEventListener('click', async () => {
      if (navigator.share) {
        try {
          await navigator.share({
            title: 'Kết quả tư vấn ngành học - FPT Polytechnic',
            text: 'Xem kết quả tư vấn ngành học của tôi',
            url: url
          });
        } catch (e) {
          copyToClipboard();
        }
      } else {
        copyToClipboard();
      }
    });

    function copyToClipboard() {
      document.getElementById('btn-copy').click();
    }
  }

  function addScrollAnimations() {
    const cards = document.querySelectorAll('.result-card, .action-card');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    cards.forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(card);
    });
  }

  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      z-index: 10000;
      font-weight: 500;
      transform: translateX(100%);
      transition: transform 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
      notification.style.transform = 'translateX(100%)';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }
</script>

<style>
  /* CSS Icons */
  .icon-check::before {
    content: '✓';
  }

  .icon-hash::before {
    content: '#';
  }

  .icon-clock::before {
    content: '🕒';
  }

  .icon-share::before {
    content: '📤';
  }

  .icon-lightbulb::before {
    content: '💡';
  }

  .icon-target::before {
    content: '🎯';
  }

  .icon-check-small::before {
    content: '✓';
  }

  .icon-star::before {
    content: '⭐';
  }

  .icon-calendar::before {
    content: '📅';
  }

  .icon-layers::before {
    content: '📚';
  }

  .icon-plus::before {
    content: '+';
  }

  .icon-download::before {
    content: '⬇️';
  }

  .icon-share-2::before {
    content: '↗️';
  }

  /* Hero Section */
  .result-hero {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 40px;
    margin-bottom: 32px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 40px;
    align-items: center;
    overflow: hidden;
    color: white;
  }

  .hero-background {
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 8px 16px;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
  }

  .hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    line-height: 1.2;
  }

  .hero-subtitle {
    font-size: 1.1rem;
    margin: 0 0 16px 0;
    opacity: 0.9;
  }

  .highlight {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
  }

  .hero-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    opacity: 0.8;
  }

  /* QR Card */
  .qr-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    color: #1f2937;
    min-width: 200px;
  }

  .qr-header h3 {
    margin: 0 0 4px 0;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .qr-header p {
    margin: 0 0 20px 0;
    font-size: 13px;
    opacity: 0.7;
  }

  .qr-container {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
  }

  .qr-container canvas,
  .qr-fallback {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .qr-share-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
  }

  .qr-share-btn:hover {
    background: #2563eb;
  }

  /* Grid Layout */
  .result-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .result-card.full-width {
    grid-column: 1 / -1;
  }

  /* Card Styles */
  .result-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .result-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  }

  .card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 24px 24px 16px 24px;
    border-bottom: 1px solid #f1f5f9;
  }

  .card-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .card-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
  }

  .card-content {
    padding: 24px;
  }

  /* Why Card */
  .why-text {
    font-size: 16px;
    line-height: 1.6;
    color: #4b5563;
    margin: 0;
  }

  /* Skills Card */
  .skills-grid {
    display: grid;
    gap: 12px;
  }

  .skill-tag {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #3b82f6;
    font-weight: 500;
    color: #1f2937;
  }

  .skill-tag .icon-check-small {
    color: #10b981;
    font-weight: bold;
  }

  /* Courses Card */
  .courses-list {
    display: grid;
    gap: 16px;
  }

  .course-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 16px;
    align-items: center;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
  }

  .course-semester {
    background: #3b82f6;
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .course-info h4 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
  }

  .course-code {
    font-size: 12px;
    color: #6b7280;
    font-family: monospace;
  }

  .course-credits {
    background: #10b981;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Study Plan Table */
  .study-plan-table {
    background: #f8fafc;
    border-radius: 16px;
    overflow: hidden;
  }

  .table-header {
    display: grid;
    grid-template-columns: 100px 120px 1fr 80px;
    gap: 16px;
    padding: 16px 20px;
    background: #1f2937;
    color: white;
    font-weight: 600;
    font-size: 14px;
  }

  .table-body {
    max-height: 400px;
    overflow-y: auto;
  }

  .table-row {
    display: grid;
    grid-template-columns: 100px 120px 1fr 80px;
    gap: 16px;
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    align-items: center;
  }

  .table-row:last-child {
    border-bottom: none;
  }

  .semester-badge {
    background: #3b82f6;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .credits-badge {
    background: #10b981;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .col-code {
    font-family: monospace;
    font-size: 13px;
    color: #6b7280;
  }

  .col-name {
    font-weight: 500;
    color: #1f2937;
  }

  /* Alternatives Card */
  .alternatives-grid {
    display: grid;
    gap: 12px;
  }

  .alternative-item {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #8b5cf6;
  }

  .alt-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
  }

  .alt-code {
    font-size: 13px;
    color: #6b7280;
    font-family: monospace;
  }

  /* Action Section */
  .action-section {
    margin-top: 40px;
  }

  .action-card {
    background: white;
    border-radius: 20px;
    padding: 32px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .action-card h3 {
    margin: 0 0 24px 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
  }

  .action-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 15px;
  }

  .action-btn.primary {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
  }

  .action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
  }

  .action-btn.secondary {
    background: #f8fafc;
    color: #1f2937;
    border: 1px solid #e2e8f0;
  }

  .action-btn.secondary:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .result-hero {
      grid-template-columns: 1fr;
      text-align: center;
      padding: 32px 24px;
    }

    .hero-title {
      font-size: 2rem;
    }

    .result-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }

    .table-header,
    .table-row {
      grid-template-columns: 80px 100px 1fr 60px;
      gap: 12px;
      padding: 12px 16px;
      font-size: 13px;
    }

    .action-buttons {
      flex-direction: column;
      align-items: center;
    }

    .action-btn {
      width: 100%;
      max-width: 280px;
    }
  }

  /* Dark mode support */
  @media (prefers-color-scheme: dark) {

    .result-card,
    .action-card {
      background: #1f2937;
      border-color: #374151;
    }

    .card-header {
      border-color: #374151;
    }

    .card-header h3,
    .why-text,
    .col-name,
    .alt-name,
    .action-card h3 {
      color: #e5e7eb;
    }

    .skill-tag,
    .course-item,
    .alternative-item,
    .study-plan-table {
      background: #374151;
      border-color: #4b5563;
    }

    .table-row {
      border-color: #4b5563;
    }

    .action-btn.secondary {
      background: #374151;
      border-color: #4b5563;
      color: #e5e7eb;
    }

    .action-btn.secondary:hover {
      background: #4b5563;
    }
  }
</style>