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
</head>
<body>
<a href="#main" class="skip-link"><?php echo $lang==='ar'?'انتقل إلى المحتوى':'Skip to content'; ?></a>

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