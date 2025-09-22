<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$currentUser = $_SESSION['username']; // logged-in username
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1c1c1c, #2a2a2a, #3a3a3a);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      color: #f1f1f1;
    }
    .chat-container {
      background: linear-gradient(135deg, #2b2b2b, #333, #3d3d3d);
      border-radius: 15px;
      box-shadow: 0px 8px 25px rgba(0,0,0,0.6);
      width: 100%;
      max-width: 650px;
      height: 85vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .chat-header {
      background: linear-gradient(135deg, #1f1f1f, #2d2d2d);
      color: #fff;
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #444;
    }
    .chat-header h5 {
      margin: 0;
      font-weight: 600;
    }
    .chat-box {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      background: linear-gradient(135deg, #2a2a2a, #222, #2f2f2f);
      display: flex;
      flex-direction: column;
    }
    .message {
      margin-bottom: 12px;
      padding: 10px 14px;
      border-radius: 15px;
      max-width: 70%;
      word-wrap: break-word;
      color: #fff;
    }
    .message.own {
      align-self: flex-end;
      background: linear-gradient(135deg, #0d6efd, #084298);
    }
    .message.other {
      align-self: flex-start;
      background: linear-gradient(135deg, #444, #222);
    }
    .meta {
      font-size: 0.75rem;
      margin-bottom: 4px;
      opacity: 0.8;
    }
    .chat-form {
      padding: 12px;
      border-top: 1px solid #444;
      background: linear-gradient(135deg, #1f1f1f, #2a2a2a);
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .chat-form textarea {
      border-radius: 10px;
      padding: 10px;
      background: #333;
      border: 1px solid #555;
      color: #f1f1f1;
      resize: none;
    }
    .chat-form textarea::placeholder {
      color: #aaa;
    }
    .chat-form button {
      border-radius: 10px;
      background: linear-gradient(135deg, #0d6efd, #084298);
      color: #fff;
      font-weight: bold;
      padding: 10px 20px;
      border: none;
      transition: 0.3s;
    }
    .chat-form button:hover {
      background: linear-gradient(135deg, #084298, #052c65);
    }
    a.btn-sm {
      color: #fff !important;
      background: #444;
      border: none;
    }
    a.btn-sm:hover {
      background: #666;
    }
  </style>
</head>
<body>
  <div class="chat-container">
    <div class="chat-header">
      <h5>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h5>
      <a href="logout.php" class="btn btn-sm">Logout</a>
    </div>
    <div id="chat" class="chat-box"></div>
    <form id="form" class="chat-form" onsubmit="return sendMessage(event)">
      <textarea id="message" placeholder="Type your message..." rows="1" maxlength="5000" required></textarea>
      <button type="submit">Send</button>
    </form>
  </div>

<script>
let lastId = 0;
const chatEl = document.getElementById('chat');
const currentUser = "<?php echo addslashes($currentUser); ?>"; // PHP username in JS

function escapeHtml(str) {
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
}

function renderMessages(messages) {
  messages.forEach(m => {
    lastId = Math.max(lastId, parseInt(m.id));
    const div = document.createElement('div');
    div.className = 'message ' + (m.username === currentUser ? 'own' : 'other');
    div.dataset.id = m.id;
    const time = new Date(m.created_at).toLocaleTimeString();

    div.innerHTML = `
      <div class="meta">${escapeHtml(m.username)} • ${time}</div>
      <div class="text">${escapeHtml(m.message)}</div>
    `;
    chatEl.appendChild(div);
  });
  chatEl.scrollTop = chatEl.scrollHeight;
}

async function fetchMessages() {
  const res = await fetch(`fetch_messages.php?after_id=${lastId}`);
  const data = await res.json();
  if (data.status === 'ok' && data.messages.length) {
    renderMessages(data.messages);
  }
}

async function sendMessage(e) {
  e.preventDefault();
  const message = document.getElementById('message').value.trim();
  if (!message) return;

  const formData = new FormData();
  formData.append('message', message);
  formData.append('username', currentUser);

  const res = await fetch('send_message.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.status === 'ok') {
    document.getElementById('message').value = '';
    fetchMessages();
  }
  return false;
}

(async function init() {
  const res = await fetch('fetch_messages.php?after_id=0');
  const data = await res.json();
  if (data.status === 'ok') renderMessages(data.messages);
  setInterval(fetchMessages, 1500);
})();
</script>
</body>
</html>
