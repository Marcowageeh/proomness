<?php
// admin/chat.php – interactive chat interface with OpenAI for administrators
// This page allows an authenticated administrator to converse with the configured
// OpenAI model. All messages are sent to the API via /api/chat.php, and the
// response is displayed in real‑time. Conversation history is stored in the
// session on the server side.

require_once __DIR__ . '/_header.php';
csrf_verify();
require_admin();

// Pull OpenAI settings to determine whether the API key is set
$AI = get_ai_settings();
$apiMissing = empty($AI['api_key']);
?>

<h1>الدردشة مع OpenAI</h1>

<?php if ($apiMissing): ?>
  <div class="card" style="margin-bottom:20px;">
    <strong>تنبيه:</strong> لم يتم إعداد مفتاح OpenAI API بعد. يرجى إدخال المفتاح من خلال صفحة
    <a href="/admin/ai_settings.php">إعدادات OpenAI</a> قبل استخدام الدردشة.
  </div>
<?php endif; ?>

<div class="card" id="chatBox" style="height:400px; overflow-y:auto; padding:12px; margin-bottom:12px;">
  <!-- Conversation will appear here -->
</div>

<form id="chatForm" class="form" style="display:flex; gap:8px; margin-top:12px;" onsubmit="return false;">
  <input type="text" id="chatInput" class="input" placeholder="اكتب رسالتك هنا..." style="flex:1;" <?php echo $apiMissing ? 'disabled' : ''; ?>>
  <button type="submit" class="btn btn-primary" <?php echo $apiMissing ? 'disabled' : ''; ?>>إرسال</button>
</form>

<style>
/* Basic styling for the admin chat messages */
#chatBox {
  background: var(--surface-2);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 12px;
  font-size: 0.95rem;
  line-height: 1.6;
}
.chat-user {
  text-align: right;
  margin: 4px 0;
  padding: 6px 10px;
  border-radius: 12px;
  background: var(--primary);
  color: #fff;
  max-width: 80%;
  margin-inline-start: auto;
}
.chat-assistant {
  text-align: left;
  margin: 4px 0;
  padding: 6px 10px;
  border-radius: 12px;
  background: var(--surface);
  color: var(--text);
  max-width: 80%;
}
</style>

<script>
// JavaScript to handle chat sending and receiving
(function(){
  const chatBox = document.getElementById('chatBox');
  const chatForm = document.getElementById('chatForm');
  const chatInput = document.getElementById('chatInput');

  // Append a message to the chat box
  function appendMessage(role, content) {
    const p = document.createElement('div');
    p.className = role === 'user' ? 'chat-user' : 'chat-assistant';
    // Basic sanitization to prevent HTML injection
    p.textContent = content;
    chatBox.appendChild(p);
    // Scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  // Send the message to the API and handle the response
  function sendMessage(msg) {
    appendMessage('user', msg);
    fetch('/api/chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg })
    }).then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.reply) {
          appendMessage('assistant', data.reply);
        } else {
          appendMessage('assistant', data.error || 'Unknown error');
        }
      }).catch(function(err) {
        appendMessage('assistant', 'حدث خطأ في الاتصال: ' + err.message);
      });
  }

  // Form submission handler
  chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const msg = chatInput.value.trim();
    if (!msg) return;
    chatInput.value = '';
    sendMessage(msg);
  });
})();
</script>

<?php include __DIR__.'/_footer.php'; ?>