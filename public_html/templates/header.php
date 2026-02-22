<?php
require_once __DIR__ . '/../inc/functions.php';
$S = get_settings();
$lang = get_lang();
$D   = get_design();
$_pk = $page_key ?? 'home';
?>
<!DOCTYPE html>
<html lang="<?php echo esc($lang); ?>" dir="<?php echo $lang==='ar'?'rtl':'ltr'; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if(!empty($custom_seo) && !empty($seo_title)){ seo_meta_custom($seo_title, $seo_desc ?? '', $seo_image ?? null); } else { seo_meta($_pk); } ?>
  <link rel="icon" type="image/png" href="/<?php echo esc($S['logos']['primary']); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <?php design_font_import(); ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/styles.css">
  <?php design_theme_link(); ?>
  <?php design_css(); ?>
  <?php design_font_style(); ?>
  <style>
/* ── Live Radio Top Bar ── */
.live-radio-bar{background:linear-gradient(135deg,#dc2626,#b91c1c,#991b1b);color:#fff;padding:.5rem 0;font-size:.82rem;position:relative;z-index:1100;box-shadow:0 2px 12px rgba(220,38,38,.35);animation:liveBarSlide .4s ease;min-height:42px}
@keyframes liveBarSlide{from{transform:translateY(-100%);opacity:0}to{transform:translateY(0);opacity:1}}
.live-radio-badge{display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.18);padding:3px 10px;border-radius:20px;font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em}
.live-dot{width:8px;height:8px;border-radius:50%;background:#fff;animation:liveDotPulse 1.2s infinite}
@keyframes liveDotPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
.live-radio-title{font-weight:600;opacity:.92;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px}
.live-radio-btn{background:rgba(255,255,255,.2);border:none;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s,transform .1s}
.live-radio-btn:hover{background:rgba(255,255,255,.35);transform:scale(1.08)}
.live-radio-btn:active{transform:scale(.95)}
.live-radio-eq{display:flex;align-items:flex-end;gap:2px;height:18px}
.live-radio-eq span{width:3px;background:#fff;border-radius:2px;opacity:.7;height:4px;transition:height .2s}
.live-radio-eq.playing span{animation:eqBounce .6s ease infinite alternate}
.live-radio-eq.playing span:nth-child(1){animation-delay:0s;height:14px}
.live-radio-eq.playing span:nth-child(2){animation-delay:.1s;height:8px}
.live-radio-eq.playing span:nth-child(3){animation-delay:.2s;height:16px}
.live-radio-eq.playing span:nth-child(4){animation-delay:.15s;height:10px}
.live-radio-eq.playing span:nth-child(5){animation-delay:.25s;height:12px}
@keyframes eqBounce{0%{height:4px}100%{height:18px}}
.live-radio-vol{-webkit-appearance:none;appearance:none;width:70px;height:4px;background:rgba(255,255,255,.3);border-radius:4px;outline:none;cursor:pointer}
.live-radio-vol::-webkit-slider-thumb{-webkit-appearance:none;width:12px;height:12px;border-radius:50%;background:#fff;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.2)}
.live-radio-vol::-moz-range-thumb{width:12px;height:12px;border-radius:50%;background:#fff;cursor:pointer;border:none}
.live-radio-close{background:none;border:none;color:rgba(255,255,255,.7);font-size:1.3rem;cursor:pointer;padding:0 4px;line-height:1;transition:color .15s}
.live-radio-close:hover{color:#fff}
@media(max-width:576px){.live-radio-title{max-width:100px;font-size:.75rem}.live-radio-vol{width:50px}}
  </style>
</head>
<body>
<a href="#main" class="skip-link"><?php echo $lang==='ar'?'انتقل إلى المحتوى':'Skip to content'; ?></a>

<?php
// ── Live Radio Top Bar ──
$_R = load_json('radio.json');
$_hasAudio = false;
$_audioSrc = '';
if(($_R['audio']['type'] ?? '') === 'file' && !empty($_R['audio']['file'])){
  $_hasAudio = true;
  $_audioSrc = '/' . $_R['audio']['file'];
} elseif(($_R['audio']['type'] ?? '') === 'stream' && !empty($_R['audio']['url'])){
  $_hasAudio = true;
  $_audioSrc = $_R['audio']['url'];
}
if($_hasAudio):
?>
<div class="live-radio-bar" id="liveRadioBar">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <span class="live-radio-badge">
        <span class="live-dot"></span>
        <?php echo $lang==='ar'?'بث مباشر':'LIVE'; ?>
      </span>
      <span class="live-radio-title"><?php echo esc(t($_R['title'] ?? [])) ?: ($lang==='ar'?'البث المباشر':'Live Broadcast'); ?></span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button class="live-radio-btn" id="liveRadioToggle" aria-label="Play/Pause" title="<?php echo $lang==='ar'?'تشغيل / إيقاف':'Play / Pause'; ?>">
        <svg id="livePlayIcon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
        <svg id="livePauseIcon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="display:none"><rect x="5" y="3" width="4" height="18"/><rect x="15" y="3" width="4" height="18"/></svg>
      </button>
      <div class="live-radio-eq" id="liveEq">
        <span></span><span></span><span></span><span></span><span></span>
      </div>
      <input type="range" class="live-radio-vol" id="liveRadioVol" min="0" max="100" value="70" title="<?php echo $lang==='ar'?'الصوت':'Volume'; ?>">
      <button class="live-radio-close" id="liveRadioClose" aria-label="Close" title="<?php echo $lang==='ar'?'إغلاق':'Close'; ?>">&times;</button>
    </div>
  </div>
  <audio id="liveRadioAudio" preload="none">
    <source src="<?php echo esc($_audioSrc); ?>">
  </audio>
</div>
<script>
(function(){
  var audio = document.getElementById('liveRadioAudio');
  var btn = document.getElementById('liveRadioToggle');
  var playIcon = document.getElementById('livePlayIcon');
  var pauseIcon = document.getElementById('livePauseIcon');
  var eq = document.getElementById('liveEq');
  var vol = document.getElementById('liveRadioVol');
  var closeBtn = document.getElementById('liveRadioClose');
  var bar = document.getElementById('liveRadioBar');
  if(!audio || !btn) return;

  audio.volume = 0.7;

  btn.addEventListener('click', function(){
    if(audio.paused){
      audio.play().then(function(){
        playIcon.style.display='none';
        pauseIcon.style.display='block';
        eq.classList.add('playing');
      }).catch(function(){});
    } else {
      audio.pause();
      playIcon.style.display='block';
      pauseIcon.style.display='none';
      eq.classList.remove('playing');
    }
  });

  vol.addEventListener('input', function(){
    audio.volume = this.value / 100;
  });

  closeBtn.addEventListener('click', function(){
    audio.pause();
    bar.style.animation = 'liveBarSlide .3s ease reverse forwards';
    setTimeout(function(){ bar.style.display = 'none'; }, 300);
  });

  audio.addEventListener('ended', function(){
    playIcon.style.display='block';
    pauseIcon.style.display='none';
    eq.classList.remove('playing');
  });
})();
</script>
<?php endif; ?>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php?lang=<?php echo esc($lang); ?>">
      <img src="/<?php echo esc($S['logos']['primary']); ?>" alt="<?php echo esc($S['site_name']); ?>">
      <div>
        <div class="brand-text"><?php echo esc($S['site_name']); ?></div>
        <div class="brand-slogan"><?php echo esc(t($S['slogan'])); ?></div>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarMain">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if(!isset($D['show_home']) || $D['show_home'] === '1'): ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='home'?' active':''; ?>" href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الرئيسية':'Home'; ?></a></li>
        <?php endif; ?>
        <?php if(!isset($D['show_services']) || $D['show_services'] === '1'): ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='services'?' active':''; ?>" href="/services.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الخدمات':'Services'; ?></a></li>
        <?php endif; ?>
        <?php if(!isset($D['show_pricing']) || $D['show_pricing'] === '1'): ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='pricing'?' active':''; ?>" href="/pricing.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الأسعار':'Pricing'; ?></a></li>
        <?php endif; ?>
        <?php if(!isset($D['show_blog']) || $D['show_blog'] === '1'): ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='blog'?' active':''; ?>" href="/blog.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'المدونة':'Blog'; ?></a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='works'?' active':''; ?>" href="/works.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'أعمالنا':'Our Work'; ?></a></li>
        <?php if(!isset($D['show_contact']) || $D['show_contact'] === '1'): ?>
        <li class="nav-item"><a class="nav-link<?php echo $_pk==='contact'?' active':''; ?>" href="/contact.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'اتصل بنا':'Contact'; ?></a></li>
        <?php endif; ?>
        <?php
        // "More" dropdown for secondary pages
        $more = [];
        if(!isset($D['show_about']) || $D['show_about'] === '1') $more[] = ['about.php', $lang==='ar'?'من نحن':'About'];
        if(!isset($D['show_faq']) || $D['show_faq'] === '1') $more[] = ['faq.php', $lang==='ar'?'الأسئلة الشائعة':'FAQ'];
        if(!isset($D['show_support']) || $D['show_support'] === '1') $more[] = ['support.php', $lang==='ar'?'الدعم':'Support'];
        if(!isset($D['show_radio']) || $D['show_radio'] === '1') $more[] = ['radio.php', $lang==='ar'?'الراديو':'Radio'];
        if(count($more)): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $lang==='ar'?'المزيد':'More'; ?></a>
          <ul class="dropdown-menu">
            <?php foreach($more as $m): ?>
            <li><a class="dropdown-item" href="/<?php echo $m[0]; ?>?lang=<?php echo esc($lang); ?>"><?php echo $m[1]; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endif; ?>
      </ul>

      <div class="d-flex align-items-center gap-2 ms-lg-3">
        <div class="dropdown">
          <button class="btn btn-ghost dropdown-toggle" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Language">
            <?php echo strtoupper($lang); ?>
          </button>
          <ul class="dropdown-menu text-center" aria-labelledby="langDropdown">
            <?php foreach(($S['languages']??[]) as $lg): ?>
              <li><a class="dropdown-item" href="?lang=<?php echo esc($lg); ?>"><?php echo strtoupper($lg); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <button id="themeToggle" type="button" aria-label="Toggle theme"></button>
        <?php if(!isset($D['show_start']) || $D['show_start'] === '1'): ?>
        <a class="btn btn-primary btn-sm d-none d-lg-inline-flex" href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'ابدأ الآن':'Get Started'; ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<main id="main">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  // Theme toggle
  var toggle = document.getElementById('themeToggle');
  if(!toggle) return;
  function applyTheme(th){
    document.body.dataset.theme = th;
    document.documentElement.dataset.theme = th;
    localStorage.setItem('theme', th);
    toggle.textContent = th === 'light' ? '\u{1F31E}' : '\u{1F319}';
  }
  var stored = localStorage.getItem('theme');
  if(!stored){
    stored = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }
  applyTheme(stored);
  toggle.addEventListener('click', function(){ applyTheme(document.body.dataset.theme === 'dark' ? 'light' : 'dark'); });

  // Navbar scroll class
  var nav = document.querySelector('.navbar');
  if(nav) window.addEventListener('scroll', function(){ nav.classList.toggle('scrolled', window.scrollY > 10); }, {passive:true});

  // Scroll animations (IntersectionObserver) — wait for DOM
  document.addEventListener('DOMContentLoaded', function(){
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){ entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target); } }); }, {threshold: 0.08, rootMargin: '0px 0px -40px 0px'});
      document.querySelectorAll('.fade-up').forEach(function(el){ io.observe(el); });
    } else {
      document.querySelectorAll('.fade-up').forEach(function(el){ el.classList.add('visible'); });
    }
  });
})();
</script>