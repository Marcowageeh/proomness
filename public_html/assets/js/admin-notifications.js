/**
 * Admin Real-Time Notification System v2
 * - Web Push for alerts even when browser is CLOSED
 * - LOUD multi-tone alert sound (plays twice)
 * - Full-screen flash + page shake on new notification
 * - Browser Notification with requireInteraction + vibrate
 * - Dramatic toast popups
 * - Polls every 5 seconds
 */
(function(){
  'use strict';

  var lastSeenId = 0;
  var firstLoad = true;
  var POLL_INTERVAL = 5000;
  var audioCtx = null;

  // ═══════════════════════════════════════
  //  WEB PUSH SUBSCRIPTION
  // ═══════════════════════════════════════
  function subscribeToPush(){
    if(!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    if(!window.VAPID_PUBLIC_KEY) return;
    navigator.serviceWorker.register('/sw.js').then(function(reg){
      return reg.pushManager.getSubscription().then(function(sub){
        if(sub) return sub;
        return reg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(window.VAPID_PUBLIC_KEY)
        });
      });
    }).then(function(sub){
      if(!sub) return;
      var rawKey = sub.getKey('p256dh');
      var rawAuth = sub.getKey('auth');
      fetch('/api/notifications.php?action=subscribe', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
          endpoint: sub.endpoint,
          p256dh: rawKey ? arrayToBase64(new Uint8Array(rawKey)) : '',
          auth: rawAuth ? arrayToBase64(new Uint8Array(rawAuth)) : ''
        })
      });
    }).catch(function(e){ console.log('Push sub failed:', e); });
  }

  function urlBase64ToUint8Array(b64){
    var pad = '='.repeat((4 - b64.length % 4) % 4);
    var raw = atob((b64 + pad).replace(/-/g, '+').replace(/_/g, '/'));
    var arr = new Uint8Array(raw.length);
    for(var i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
    return arr;
  }
  function arrayToBase64(a){
    var b=''; for(var i=0;i<a.length;i++) b+=String.fromCharCode(a[i]); return btoa(b);
  }

  // ═══════════════════════════════════════
  //  PERMISSION
  // ═══════════════════════════════════════
  function requestPermission(){
    if(!('Notification' in window)) return;
    if(Notification.permission === 'default'){
      Notification.requestPermission().then(function(p){
        var btn = document.getElementById('notifPermBtn');
        if(p === 'granted'){ if(btn) btn.style.display='none'; subscribeToPush(); }
      });
    } else if(Notification.permission === 'granted'){
      subscribeToPush();
    }
  }

  // ═══════════════════════════════════════
  //  LOUD SOUND — Triple-tone square wave × 2
  // ═══════════════════════════════════════
  function playLoudSound(){
    try {
      if(!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      if(audioCtx.state === 'suspended') audioCtx.resume();
      // Play alert pattern TWICE for emphasis
      alertPattern(audioCtx.currentTime);
      alertPattern(audioCtx.currentTime + 0.55);
    } catch(e){}
  }
  function alertPattern(t){
    var tones = [[880, 0.1], [1100, 0.1], [1400, 0.15], [1600, 0.18]];
    for(var i = 0; i < tones.length; i++){
      var osc = audioCtx.createOscillator();
      var gain = audioCtx.createGain();
      osc.connect(gain); gain.connect(audioCtx.destination);
      osc.type = 'square';
      osc.frequency.value = tones[i][0];
      gain.gain.setValueAtTime(0.8, t);
      gain.gain.exponentialRampToValueAtTime(0.02, t + tones[i][1]);
      osc.start(t); osc.stop(t + tones[i][1]);
      t += tones[i][1] + 0.025;
    }
  }

  // ═══════════════════════════════════════
  //  BROWSER NOTIFICATION
  // ═══════════════════════════════════════
  function showBrowserNotification(title, body, link){
    if(!('Notification' in window) || Notification.permission !== 'granted') return;
    var n = new Notification(title, {
      body: body,
      icon: '/assets/images/logo1.png',
      badge: '/assets/images/logo1.png',
      tag: 'proomnes-' + Date.now(),
      requireInteraction: true,
      vibrate: [300, 100, 300, 100, 500, 200, 300],
      silent: false
    });
    if(link){ n.onclick = function(){ window.focus(); window.location.href = link; n.close(); }; }
    setTimeout(function(){ n.close(); }, 60000);
  }

  // ═══════════════════════════════════════
  //  FULL-SCREEN FLASH + SHAKE
  // ═══════════════════════════════════════
  function flashScreen(){
    var overlay = document.getElementById('notifFlash');
    if(!overlay) return;
    overlay.style.display = 'block';
    overlay.style.animation = 'none';
    overlay.offsetHeight;
    overlay.style.animation = 'notifFlashAnim 2s ease-out';
    setTimeout(function(){ overlay.style.display = 'none'; }, 2000);
    document.body.classList.add('notif-shake');
    setTimeout(function(){ document.body.classList.remove('notif-shake'); }, 700);
  }

  // ═══════════════════════════════════════
  //  TOAST NOTIFICATIONS
  // ═══════════════════════════════════════
  function showToast(title, body, type, link){
    var c = document.getElementById('toastContainer');
    if(!c) return;
    var t = document.createElement('div');
    t.className = 'notif-toast notif-toast-' + (type || 'info');
    var icons = {lead:'📋', contact:'✉️', chat:'💬', system:'⚙️', info:'🔔'};
    t.innerHTML =
      '<div class="notif-toast-icon">' + (icons[type] || icons.info) + '</div>' +
      '<div class="notif-toast-content">' +
        '<strong>' + escHtml(title) + '</strong>' +
        (body ? '<p>' + escHtml(body) + '</p>' : '') +
      '</div>' +
      '<button class="notif-toast-close" onclick="this.parentNode.remove()">&times;</button>';
    if(link){
      t.style.cursor = 'pointer';
      t.addEventListener('click', function(e){ if(e.target.tagName!=='BUTTON') window.location.href=link; });
    }
    c.prepend(t);
    setTimeout(function(){ if(t.parentNode) t.classList.add('notif-toast-exit'); }, 20000);
    setTimeout(function(){ if(t.parentNode) t.remove(); }, 20500);
  }

  // ═══════════════════════════════════════
  //  BADGE + DROPDOWN
  // ═══════════════════════════════════════
  function updateBadge(count){
    var b = document.getElementById('notifBadge');
    if(!b) return;
    if(count > 0){ b.textContent = count > 99 ? '99+' : count; b.style.display='flex'; }
    else { b.textContent=''; b.style.display='none'; }
  }
  function updateDropdown(notifs){
    var list = document.getElementById('notifList');
    if(!list) return;
    var empty = document.getElementById('notifEmpty');
    if(notifs.length===0){ if(empty) empty.style.display='block'; return; }
    if(empty) empty.style.display='none';
    notifs.forEach(function(n){
      if(list.querySelector('[data-nid="'+n.id+'"]')) return;
      var a = document.createElement('a');
      a.className = 'notif-item' + (n.is_read===0?' notif-unread':'');
      a.href = n.link || '/admin/leads.php';
      a.dataset.nid = n.id;
      var ic = {lead:'📋',contact:'✉️',chat:'💬',system:'⚙️'};
      a.innerHTML =
        '<span class="notif-item-icon">'+(ic[n.type]||'🔔')+'</span>' +
        '<div class="notif-item-body">' +
          '<div class="notif-item-title">'+escHtml(n.title)+'</div>' +
          (n.body?'<div class="notif-item-text">'+escHtml(n.body).substring(0,100)+'</div>':'') +
          '<div class="notif-item-time">'+formatTimeAgo(n.created_at)+'</div>' +
        '</div>';
      a.addEventListener('click', function(){ markRead([n.id]); });
      list.prepend(a);
    });
  }

  // ═══════════════════════════════════════
  //  MARK READ
  // ═══════════════════════════════════════
  function markRead(ids){
    fetch('/api/notifications.php?action=read',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({ids:ids})});
  }
  function markAllRead(){
    fetch('/api/notifications.php?action=read_all',{method:'POST',credentials:'same-origin'}).then(function(r){return r.json()}).then(function(){
      updateBadge(0);
      document.querySelectorAll('.notif-unread').forEach(function(el){el.classList.remove('notif-unread');});
    });
  }

  // ═══════════════════════════════════════
  //  POLLING (every 5s)
  // ═══════════════════════════════════════
  function poll(){
    fetch('/api/notifications.php?action=poll&since='+lastSeenId,{credentials:'same-origin'})
      .then(function(r){return r.ok?r.json():null})
      .then(function(data){
        if(!data) return;
        updateBadge(data.unread);
        var notifs = data.notifications || [];

        // Detect genuinely new ones
        var newOnes = [];
        notifs.forEach(function(n){
          var nid = parseInt(n.id,10);
          if(nid > lastSeenId && !firstLoad) newOnes.push(n);
        });
        // Update highwater mark
        notifs.forEach(function(n){
          var nid = parseInt(n.id,10);
          if(nid > lastSeenId) lastSeenId = nid;
        });

        updateDropdown(notifs);

        if(firstLoad){ firstLoad = false; return; }

        // ALERT for new notifications
        if(newOnes.length > 0){
          newOnes.forEach(function(n){
            playLoudSound();
            flashScreen();
            showToast(n.title, n.body||'', n.type, n.link);
            showBrowserNotification(
              '🚨 ' + n.title,
              n.body || 'إشعار جديد من الموقع',
              n.link ? (window.location.origin + n.link) : ''
            );
          });
        }
      })
      .catch(function(){})
      .finally(function(){ setTimeout(poll, POLL_INTERVAL); });
  }

  // ═══════════════════════════════════════
  //  HELPERS
  // ═══════════════════════════════════════
  function escHtml(s){ var d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
  function formatTimeAgo(ds){
    if(!ds) return '';
    var diff=Math.floor((new Date()-new Date(ds))/1000);
    if(diff<60) return 'الآن';
    if(diff<3600) return Math.floor(diff/60)+' د';
    if(diff<86400) return Math.floor(diff/3600)+' س';
    return Math.floor(diff/86400)+' ي';
  }

  // ═══════════════════════════════════════
  //  GLOBALS
  // ═══════════════════════════════════════
  window.toggleNotifPanel = function(){
    var p=document.getElementById('notifPanel');
    if(p) p.style.display = p.style.display==='block'?'none':'block';
  };
  window.markAllNotifRead = markAllRead;

  document.addEventListener('click', function(e){
    var p=document.getElementById('notifPanel'), b=document.getElementById('notifBell');
    if(p&&p.style.display==='block'&&!p.contains(e.target)&&e.target!==b&&!b.contains(e.target)) p.style.display='none';
  });

  // ═══════════════════════════════════════
  //  INIT
  // ═══════════════════════════════════════
  document.addEventListener('DOMContentLoaded', function(){
    requestPermission();
    poll();
  });

})();