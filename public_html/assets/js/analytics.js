(function(){
  function send(evt, data){
    try{ navigator.sendBeacon('/api/analytics.php', JSON.stringify({evt, data, ts:Date.now(), url:location.href, ua:navigator.userAgent})) }catch(e){}
  }
  // Page view
  send('view', {ref: document.referrer});
  // Clicks
  document.addEventListener('click', function(e){
    var t=e.target.closest('a,button');
    if(!t) return;
    send('click', {text:(t.innerText||'').slice(0,64), href:t.href||null});
  });
  // Form submits
  document.addEventListener('submit', function(e){
    var f=e.target;
    var id = f.getAttribute('id') || f.getAttribute('name') || f.action || 'form';
    send('form_submit', {id:id});
  }, true);
  // Time on page (every 15s)
  var i=0; setInterval(function(){ i+=15; send('dwell',{seconds:i}); }, 15000);
})();