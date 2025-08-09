<section class="card">
  <h2>Thống kê ngành được đề xuất</h2>
  <div class="table">
    <div class="table-row table-head">
      <div>Ngành</div>
      <div>Số lượt đề xuất</div>
    </div>
    <?php foreach ($summary as $row): ?>
      <div class="table-row">
        <div><?= htmlspecialchars($row['major_name']) ?></div>
        <div><?= (int) $row['total'] ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div style="display:flex; gap:16px; flex-wrap:wrap; align-items:flex-start;">
    <canvas id="chartBar" height="200"></canvas>
    <canvas id="chartPie" height="200"></canvas>
  </div>
  <div class="actions">
    <button class="btn" id="btn-export-csv">Tải CSV</button>
    <button class="btn" id="btn-export-png">Tải ảnh biểu đồ</button>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const data = <?php echo json_encode($summary, JSON_UNESCAPED_UNICODE); ?>;
  const labels = data.map(i => i.major_name || 'Khác');
  const values = data.map(i => Number(i.total));
  const bar = new Chart(document.getElementById('chartBar').getContext('2d'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Số lượt', data: values, backgroundColor: '#ff6a00' }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
  const pie = new Chart(document.getElementById('chartPie').getContext('2d'), {
    type: 'pie',
    data: { labels, datasets: [{ data: values, backgroundColor: labels.map((_, i) => `hsl(${(i * 60) % 360} 80% 60%)`) }] },
    options: { responsive: true }
  });
  document.getElementById('btn-export-csv').addEventListener('click', () => {
    const rows = [['Ngành', 'Số lượt'], ...data.map(i => [i.major_name || 'Khác', i.total])];
    const csv = rows.map(r => r.map(v => `"${String(v).replaceAll('"', '""')}"`).join(',')).join('\n');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
    a.download = 'stats.csv';
    a.click();
  });
  document.getElementById('btn-export-png').addEventListener('click', async () => {
    const c1 = document.getElementById('chartBar');
    const c2 = document.getElementById('chartPie');
    const a = document.createElement('a');
    a.href = c1.toDataURL('image/png'); a.download = 'stats-bar.png'; a.click();
    const a2 = document.createElement('a');
    a2.href = c2.toDataURL('image/png'); a2.download = 'stats-pie.png'; a2.click();
  });
</script>