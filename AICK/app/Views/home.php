<section class="hero">
  <h1>Tư vấn chọn ngành dựa trên AI</h1>
  <p>Nhập thông tin bên dưới để nhận đề xuất ngành học phù hợp tại FPT Polytechnic.</p>
</section>

<section class="card">
  <?php $baseUrl = $baseUrl ?? '';
  $baseIndex = $baseIndex ?? $baseUrl . '/index.php'; ?>
  <!-- Luôn post qua index.php để tránh phụ thuộc rewrite -->
  <form method="post" action="<?= $baseIndex ?>/recommend" class="grid-form">
    <div class="field">
      <label>Họ và tên</label>
      <input name="name" required placeholder="Nguyễn Văn A" />
    </div>
    <div class="field">
      <label>Tuổi</label>
      <input name="age" type="number" min="14" max="80" />
    </div>
    <div class="field span-2">
      <label>Sở thích</label>
      <textarea name="interests" rows="2" placeholder="VD: vẽ, chụp ảnh, lập trình, chơi game, marketing..."></textarea>
    </div>
    <div class="field span-2">
      <label>Kỹ năng / Tố chất</label>
      <textarea name="strengths" rows="2"
        placeholder="VD: tư duy logic, sáng tạo, giao tiếp tốt, kiên trì..."></textarea>
    </div>
    <div class="field">
      <label>Môn học yêu thích</label>
      <input name="favorite_subjects" placeholder="Toán, Lý, Văn, ..." />
    </div>
    <div class="field">
      <label>Điểm số các môn (tùy chọn)</label>
      <input name="scores" placeholder="Toán:8; Văn:6.5; Anh:7; ..." />
    </div>
    <div class="field">
      <label>Định hướng nghề nghiệp</label>
      <input name="career_orientation" placeholder="VD: designer, developer, marketer, kỹ sư..." />
    </div>
    <div class="field">
      <label>Thói quen học tập</label>
      <input name="learning_style" placeholder="Thực hành, lý thuyết, làm việc nhóm..." />
    </div>
    <div class="field span-2">
      <label>Mức độ yêu thích (công nghệ / sáng tạo / giao tiếp / logic)</label>
      <input name="affinities" placeholder="VD: công nghệ cao; sáng tạo trung bình; logic cao..." />
    </div>

    <div class="actions">
      <button class="btn btn-primary" type="submit">Nhận tư vấn</button>
    </div>
  </form>
</section>

<section class="card">
  <h3>Danh sách ngành hiện có</h3>
  <div class="chips">
    <?php foreach ($majors as $m): ?>
      <span class="chip" title="<?= htmlspecialchars($m['code']) ?>"><?= htmlspecialchars($m['name']) ?></span>
    <?php endforeach; ?>
  </div>
</section>