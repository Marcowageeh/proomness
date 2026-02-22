/**
 * Client Push Notifications — public website visitors
 * Registers Service Worker, subscribes to Web Push, plays loud alert
 */
(function(){
  'use strict';

  var VAPID_KEY = window.VAPID_PUBLIC_KEY || '';
  if(!VAPID_KEY) return;

  // ── Loud Alert Sound (square wave, 4 tones × 2 repeats, gain 0.8) ──
  function playLoudAlert(){
    try {
      var AC = window.AudioContext || window.webkitAudioContext;
      if(!AC) return;
      var ctx = new AC();
      var freqs = [880, 1100, 880, 1100]; // Hz
      var dur = 0.15; // seconds per tone
      var gap = 0.05;
      var repeats = 2;
      var t = ctx.currentTime;
      for(var r = 0; r < repeats; r++){
        for(var i = 0; i < freqs.length; i++){
          var osc = ctx.createOscillator();
          var gain = ctx.createGain();
          osc.type = 'square';
          osc.frequency.value = freqs[i];
          gain.gain.value = 0.8;
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(t);
          osc.stop(t + dur);
          t += dur + gap;
        }
        t += 0.1; // gap between repeats
      }
      setTimeout(function(){ ctx.close(); }, 3000);
    } catch(e){}
  }

  // ── Full-Screen Flash ──
  function showFlash(){
    var flash = document.getElementById('clientNotifFlash');
    if(!flash) return;
    flash.style.display = 'block';
    flash.style.animation = 'clientFlashAnim .8s ease forwards';
    setTimeout(function(){
      flash.style.display = 'none';
      flash.style.animation = '';
    }, 900);
  }

  // ── Toast Notification ──
  function showToast(title, body, link){
    var container = document.getElementById('clientToastContainer');
    if(!container) return;
    var toast = document.createElement('div');
    toast.className = 'client-toast';
    toast.innerHTML = '<div class="client-toast-icon">🔔</div>' +
      '<div class="client-toast-body">' +
        '<strong>' + escHtml(title) + '</strong>' +
        '<p>' + escHtml(body) + '</p>' +
      '</div>' +
      '<button class="client-toast-close" onclick="this.parentNode.remove()">&times;</button>';
    if(link){
      toast.style.cursor = 'pointer';
      toast.addEventListener('click', function(e){
        if(e.target.tagName === 'BUTTON') return;
        window.location.href = link;
      });
    }
    container.appendChild(toast);
    // Page shake
    document.body.classList.add('client-shake');
    setTimeout(function(){ document.body.classList.remove('client-shake'); }, 700);
    // Auto remove after 15 seconds
    setTimeout(function(){
      toast.classList.add('client-toast-exit');
      setTimeout(function(){ toast.remove(); }, 400);
    }, 15000);
  }

  function escHtml(s){
    if(!s) return '';
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  // ── Subscribe to Push ──
  function urlBase64ToUint8Array(base64String){
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData = atob(base64);
    var arr = new Uint8Array(rawData.length);
    for(var i = 0; i < rawData.length; i++) arr[i] = rawData.charCodeAt(i);
    return arr;
  }

  function subscribePush(reg){
    return reg.pushManager.getSubscription().then(function(sub){
      if(sub) return sub;
      return reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(VAPID_KEY)
      });
    }).then(function(sub){
      // Send subscription to server
      return fetch('/api/client-push.php?action=subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(sub.toJSON())
      });
    }).catch(function(err){
      console.warn('Client push subscribe failed:', err);
    });
  }

  // ── Register SW & Subscribe ──
  function init(){
    if(!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    navigator.serviceWorker.register('/sw.js').then(function(reg){
      // If already granted, subscribe silently
      if(Notification.permission === 'granted'){
        subscribePush(reg);
        hideBanner();
      } else if(Notification.permission === 'default'){
        showBanner(reg);
      }
    });

    // Listen for push messages from SW (foreground)
    navigator.serviceWorker.addEventListener('message', function(event){
      if(event.data && event.data.type === 'CLIENT_PUSH'){
        playLoudAlert();
        showFlash();
        showToast(event.data.title, event.data.body, event.data.link);
      }
    });
  }

  // ── Permission Banner ──
  function showBanner(reg){
    var banner = document.getElementById('clientPushBanner');
    if(!banner) return;
    // Show after 3 seconds to not annoy immediately
    setTimeout(function(){
      banner.style.display = 'flex';
    }, 3000);
    var btn = document.getElementById('clientPushAllow');
    if(btn){
      btn.addEventListener('click', function(){
        Notification.requestPermission().then(function(perm){
          if(perm === 'granted'){
            subscribePush(reg);
            hideBanner();
          } else {
            hideBanner();
          }
        });
      });
    }
    var dismiss = document.getElementById('clientPushDismiss');
    if(dismiss){
      dismiss.addEventListener('click', function(){
        hideBanner();
        // Don't show again this session
        sessionStorage.setItem('push_dismissed', '1');
      });
    }
  }

  function hideBanner(){
    var banner = document.getElementById('clientPushBanner');
    if(banner) banner.style.display = 'none';
  }

  // Don't show banner if already dismissed this session
  if(sessionStorage.getItem('push_dismissed') === '1'){
    // Still init SW and subscribe if already granted
    if('serviceWorker' in navigator){
      navigator.serviceWorker.register('/sw.js').then(function(reg){
        if(Notification.permission === 'granted') subscribePush(reg);
      });
    }
    return;
  }

  // Start
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
