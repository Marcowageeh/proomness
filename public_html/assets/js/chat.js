(function(){
  if(document.getElementById('aiChatWidget')) return;
  var w = document.createElement('div');
  w.id='aiChatWidget';
  w.innerHTML = '<button id="aiChatBtn" class="btn btn-primary" style="position:fixed;bottom:20px;right:20px;z-index:9999">🤖</button>' +
                '<div id="aiChatPanel" style="position:fixed;bottom:80px;right:20px;width:320px;max-height:60vh;background:var(--surface);border:1px solid rgba(0,0,0,.1);box-shadow:var(--shadow);border-radius:12px;display:none;flex-direction:column;overflow:hidden;z-index:9999">' +
                '<div style="padding:8px 12px;font-weight:700;color:var(--text);background:var(--surface-2)">Assistant</div>' +
                '<div id="aiChatLog" style="padding:12px;gap:8px;display:flex;flex-direction:column;overflow:auto"></div>' +
                '<form id="aiChatForm" style="display:flex;gap:8px;padding:8px;background:var(--surface-2)">' +
                '<input id="aiChatInput" class="input" style="flex:1" placeholder="Ask anything..." />' +
                '<button class="btn btn-primary" type="submit">Send</button>' +
                '</form></div>';
  document.body.appendChild(w);
  var btn = document.getElementById('aiChatBtn');
  var panel = document.getElementById('aiChatPanel');
  var log = document.getElementById('aiChatLog');
  btn.onclick = function(){ panel.style.display = panel.style.display==='none'?'flex':'none'; };
  document.getElementById('aiChatForm').addEventListener('submit', async function(e){
    e.preventDefault();
    var q = document.getElementById('aiChatInput').value.trim();
    if(!q) return;
    var bubble = document.createElement('div'); bubble.textContent = q; bubble.style.cssText='align-self:flex-end;background:var(--primary);color:#fff;padding:8px 10px;border-radius:10px;max-width:90%';
    log.appendChild(bubble);
    document.getElementById('aiChatInput').value='';
    try{
      const res = await fetch('/api/chat.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({q})});
      const j = await res.json();
      var a = document.createElement('div'); a.textContent = j.answer || 'No answer'; a.style.cssText='align-self:flex-start;background:var(--surface-2);color:var(--text);padding:8px 10px;border-radius:10px;max-width:90%';
      log.appendChild(a);
    }catch(err){
      var a = document.createElement('div'); a.textContent = 'Error contacting assistant'; a.style.cssText='align-self:flex-start;background:#fee;padding:8px 10px;border-radius:10px;max-width:90%';
      log.appendChild(a);
    }
    log.scrollTop = log.scrollHeight;
  });
})();