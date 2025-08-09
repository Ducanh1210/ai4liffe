<section class="card">
  <h2>Thống kê ngành được đề xuất</h2>
  <div class="table">
    <div class="table-row table-head">
      <div>Ngành</div><div>Số lượt đề xuất</div>
    </div>
    <?php foreach ($summary as $row): ?>
      <div class="table-row">
        <div><?= htmlspecialchars($row['major_name']) ?></div>
        <div><?= (int)$row['total'] ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <canvas id="chart" height="200"></canvas>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const data = <?php echo json_encode($summary, JSON_UNESCAPED_UNICODE); ?>;
  const ctx = document.getElementById('chart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(i => i.major_name || 'Khác'),
      datasets: [{
        label: 'Số lượt',
        data: data.map(i => Number(i.total)),
        backgroundColor: '#ff6a00'
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
</script>


