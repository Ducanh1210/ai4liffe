<?php
$baseUrl = $baseUrl ?? '';
$baseIndex = $baseIndex ?? $baseUrl . '/index.php';
$hasRewrite = (strpos($_SERVER['REQUEST_URI'] ?? '', '/index.php') === false);
$apiUrl = $hasRewrite ? ($baseUrl . '/api/chat') : ($baseIndex . '/api/chat');
$apiStreamUrl = $hasRewrite ? ($baseUrl . '/api/chat-stream') : ($baseIndex . '/api/chat-stream');
?>
<section class="card">
  <h2>Chat tư vấn thông minh</h2>
  <div class="chat-mode">
    <label><input type="radio" name="mode" value="advisor" checked> Tư vấn FPT</label>
    <label><input type="radio" name="mode" value="general"> Toàn năng</label>
  </div>
  <div id="chat-box" class="chat-box"></div>
  <form id="chat-form" class="chat-form">
    <input id="chat-input" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" />
    <button class="btn btn-primary" type="submit">Gửi</button>
  </form>
</section>

<script>
  const API = "<?= htmlspecialchars($apiUrl) ?>";
  const box = document.getElementById('chat-box');
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-input');
  let messages = [];
  let mode = 'advisor';

  document.querySelectorAll('input[name="mode"]').forEach(r => r.addEventListener('change', e => {
    mode = e.target.value;
    messages = []; // reset conversation when switching mode
    box.innerHTML = '';
  }));

  function append(role, content) {
    const item = document.createElement('div');
    item.className = 'chat-msg ' + role;
    item.innerText = content;
    box.appendChild(item);
    box.scrollTop = box.scrollHeight;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    input.value = '';
    messages.push({ role: 'user', content: text });
    append('user', text);
    // Try streaming first; fallback to non-stream
    try {
      await streamChat();
    } catch (e) {
      append('assistant pending', 'Đang trả lời...');
      try {
        const res = await fetch(API, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ messages, mode }) });
        const data = await res.json();
        const last = box.querySelector('.chat-msg.pending');
        if (last) last.remove();
        const aiMsgs = data.answer?.messages || [];
        const assistant = aiMsgs[aiMsgs.length - 1];
        messages = aiMsgs;
        append('assistant', assistant?.content || '');
      } catch (err) {
        const last = box.querySelector('.chat-msg.pending');
        if (last) last.remove();
        append('assistant', 'Xin lỗi, hệ thống đang bận. Vui lòng thử lại.');
      }
    }
  });

  async function streamChat() {
    const pending = document.createElement('div');
    pending.className = 'chat-msg assistant';
    pending.innerText = '';
    box.appendChild(pending);
    const resp = await fetch("<?= htmlspecialchars($apiStreamUrl) ?>", { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ messages, mode }) });
    if (!resp.ok || !resp.body) throw new Error('no-stream');
    const reader = resp.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    while (true) {
      const { value, done } = await reader.read();
      if (done) break;
      buffer += decoder.decode(value, { stream: true });
      const parts = buffer.split('\n\n');
      buffer = parts.pop();
      for (const p of parts) {
        if (!p.startsWith('data:')) continue;
        const json = p.slice(5).trim();
        try {
          const evt = JSON.parse(json);
          if (evt.type === 'token') { pending.innerText += evt.content; box.scrollTop = box.scrollHeight; }
        } catch { }
      }
    }
    messages.push({ role: 'assistant', content: pending.innerText });
  }
</script>