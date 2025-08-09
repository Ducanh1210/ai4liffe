<?php
?><!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FPT Polytechnic - Tư vấn chọn ngành AI</title>
  <?php
  $baseUrl = $baseUrl ?? '';
  $baseIndex = $baseIndex ?? $baseUrl . '/index.php';
  $hrefHome = $baseIndex . '/';
  $hrefStats = $baseIndex . '/stats';
  $hrefChat = $baseUrl . '/chat_ui.php';
  ?>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js">

  <!-- Styles -->
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/style.css">

  <!-- Meta Tags -->
  <meta name="description" content="Hệ thống tư vấn chọn ngành học thông minh dựa trên AI - FPT Polytechnic">
  <meta name="keywords" content="FPT Polytechnic, tư vấn ngành học, AI, trí tuệ nhân tạo">
  <meta name="author" content="FPT Polytechnic">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🎓%3C/text%3E%3C/svg%3E">

  <style>
    /* Loading Animation */
    .page-loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    .page-loader.hide {
      opacity: 0;
      visibility: hidden;
    }

    .loader-content {
      text-align: center;
      color: white;
    }

    .loader-spinner {
      width: 60px;
      height: 60px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top: 3px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .loader-text {
      font-size: 18px;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .loader-subtext {
      font-size: 14px;
      opacity: 0.8;
    }
  </style>
</head>

<body>
  <!-- Page Loader -->
  <div class="page-loader" id="pageLoader">
    <div class="loader-content">
      <div class="loader-spinner"></div>
      <div class="loader-text">FPT Polytechnic</div>
      <div class="loader-subtext">Đang tải hệ thống tư vấn AI...</div>
    </div>
  </div>

  <!-- Background Elements -->
  <div class="bg-elements">
    <div class="bg-gradient"></div>
    <div class="bg-dots"></div>
    <div class="bg-orbs">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="main-nav" id="mainNav">
    <div class="nav-container">
      <a class="nav-brand" href="<?= htmlspecialchars($hrefHome) ?>">
        <div class="brand-icon">
          <svg viewBox="0 0 40 40" fill="none">
            <path d="M20 2L35 12V28L20 38L5 28V12L20 2Z" fill="url(#brandGradient)" stroke="white" stroke-width="1" />
            <text x="20" y="26" text-anchor="middle" fill="white" font-size="14" font-weight="bold">FP</text>
            <defs>
              <linearGradient id="brandGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#667eea" />
                <stop offset="100%" style="stop-color:#764ba2" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="brand-text">
          <span class="brand-name">FPOLY</span>
          <span class="brand-tagline">AI Advisor</span>
        </div>
      </a>

      <div class="nav-menu" id="navMenu">
        <a class="nav-link" href="<?= htmlspecialchars($hrefHome) ?>" data-page="home">
          <i class="nav-icon" data-lucide="home"></i>
          <span>Trang chủ</span>
        </a>
        <a class="nav-link" href="<?= htmlspecialchars($hrefChat) ?>" data-page="chat">
          <i class="nav-icon" data-lucide="message-circle"></i>
          <span>Chat AI</span>
        </a>
        <a class="nav-link" href="<?= htmlspecialchars($hrefStats) ?>" data-page="stats">
          <i class="nav-icon" data-lucide="bar-chart-3"></i>
          <span>Thống kê</span>
        </a>
        <a class="nav-link nav-external" href="https://caodang.fpt.edu.vn" target="_blank" rel="noopener">
          <i class="nav-icon" data-lucide="external-link"></i>
          <span>FPT Polytechnic</span>
        </a>
      </div>

      <button class="nav-toggle" id="navToggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="main-content" id="mainContent">
    <div class="content-container">
      <?= $content ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-section">
          <h3 class="footer-title">FPT Polytechnic</h3>
          <p class="footer-desc">Hệ thống tư vấn chọn ngành học thông minh dựa trên trí tuệ nhân tạo</p>
          <div class="footer-social">
            <a href="#" class="social-link" aria-label="Facebook">
              <i data-lucide="facebook"></i>
            </a>
            <a href="#" class="social-link" aria-label="YouTube">
              <i data-lucide="youtube"></i>
            </a>
            <a href="#" class="social-link" aria-label="LinkedIn">
              <i data-lucide="linkedin"></i>
            </a>
          </div>
        </div>

        <div class="footer-section">
          <h4 class="footer-subtitle">Liên kết nhanh</h4>
          <ul class="footer-links">
            <li><a href="<?= htmlspecialchars($hrefHome) ?>">Trang chủ</a></li>
            <li><a href="<?= htmlspecialchars($hrefChat) ?>">Chat AI</a></li>
            <li><a href="<?= htmlspecialchars($hrefStats) ?>">Thống kê</a></li>
            <li><a href="https://caodang.fpt.edu.vn" target="_blank">FPT Polytechnic</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h4 class="footer-subtitle">Hỗ trợ</h4>
          <ul class="footer-links">
            <li><a href="#">Hướng dẫn sử dụng</a></li>
            <li><a href="#">Câu hỏi thường gặp</a></li>
            <li><a href="#">Liên hệ</a></li>
            <li><a href="#">Góp ý</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h4 class="footer-subtitle">Thông tin</h4>
          <div class="footer-info">
            <p><i data-lucide="map-pin"></i> 13 P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội</p>
            <p><i data-lucide="phone"></i> (024) 7300 1955</p>
            <p><i data-lucide="mail"></i> info@fpt.edu.vn</p>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> FPT Polytechnic. Built with ❤️ for Hackathon.</p>
        <div class="footer-badges">
          <span class="badge">AI Powered</span>
          <span class="badge">Modern Design</span>
          <span class="badge">Responsive</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js"></script>
  <script src="<?= $baseUrl ?>/public/js/app.js"></script>

  <script>
    // Initialize page
    document.addEventListener('DOMContentLoaded', function () {
      // Hide loader
      setTimeout(() => {
        document.getElementById('pageLoader').classList.add('hide');
      }, 800);

      // Initialize Lucide icons
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }

      // Navigation functionality
      initNavigation();

      // Smooth scrolling
      initSmoothScrolling();

      // Parallax effects
      initParallax();
    });

    function initNavigation() {
      const nav = document.getElementById('mainNav');
      const toggle = document.getElementById('navToggle');
      const menu = document.getElementById('navMenu');

      // Mobile menu toggle
      toggle.addEventListener('click', () => {
        menu.classList.toggle('active');
        toggle.classList.toggle('active');
      });

      // Scroll effect
      let lastScroll = 0;
      window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
          nav.classList.add('scrolled');
        } else {
          nav.classList.remove('scrolled');
        }

        if (currentScroll > lastScroll && currentScroll > 200) {
          nav.classList.add('hidden');
        } else {
          nav.classList.remove('hidden');
        }

        lastScroll = currentScroll;
      });

      // Active link highlighting
      const currentPath = window.location.pathname;
      document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
          link.classList.add('active');
        }
      });
    }

    function initSmoothScrolling() {
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });
    }

    function initParallax() {
      const orbs = document.querySelectorAll('.orb');

      window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;

        orbs.forEach((orb, index) => {
          const speed = (index + 1) * 0.3;
          orb.style.transform = `translateY(${rate * speed}px) rotate(${scrolled * 0.1}deg)`;
        });
      });

      // Mouse parallax
      document.addEventListener('mousemove', (e) => {
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;

        orbs.forEach((orb, index) => {
          const speed = (index + 1) * 20;
          const x = (mouseX - 0.5) * speed;
          const y = (mouseY - 0.5) * speed;

          orb.style.transform += ` translate(${x}px, ${y}px)`;
        });
      });
    }

    // Global notification system
    window.showNotification = function (message, type = 'info', duration = 4000) {
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;

      const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info'
      };

      notification.innerHTML = `
        <i data-lucide="${icons[type] || 'info'}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
          <i data-lucide="x"></i>
        </button>
      `;

      document.body.appendChild(notification);

      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }

      // Auto remove
      setTimeout(() => {
        if (notification.parentElement) {
          notification.style.opacity = '0';
          notification.style.transform = 'translateX(100%)';
          setTimeout(() => notification.remove(), 300);
        }
      }, duration);

      // Animate in
      setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
      }, 100);
    };
  </script>
</body>

</html>