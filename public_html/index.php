<?php
$page_key='home';
require __DIR__.'/templates/header.php';
$H  = load_home();
$SVC = load_services();
$D  = get_design();
?>
<section class="hero">
  <div class="container">
    <div class="inner">
      <div class="fade-up">
        <span class="badge"><?php echo esc(t($H['badge'])); ?></span>
        <h1><?php echo esc(t($H['heroTitle'])); ?></h1>
        <p><?php echo esc(t($H['heroDesc'])); ?></p>
        <div class="mt-2">
          <a class="btn btn-primary btn-lg" href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo esc(t($H['ctaPrimary'])); ?></a>
          <?php if(!isset($D['show_pricing']) || $D['show_pricing'] === '1'): ?>
          <a class="btn btn-outline btn-lg" href="/pricing.php?lang=<?php echo esc($lang); ?>"><?php echo esc(t($H['ctaSecondary'])); ?></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="visual fade-up">
        <?php if(!empty($D['hero_image'])): ?>
          <img src="/<?php echo esc($D['hero_image']); ?>" alt="<?php echo esc(t($H['heroTitle'])); ?>">
        <?php else: ?>
          <img src="/assets/images/logo2.png" alt="<?php echo esc($S['site_name']); ?>">
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if(!isset($D['show_services']) || $D['show_services'] === '1'): ?>
<section class="section" id="services">
  <div class="container">
    <div class="section-header fade-up">
      <h2><?php echo $lang==='ar'?'ماذا نفعل؟':'What We Do'; ?></h2>
      <p class="lead"><?php echo $lang==='ar'?'نُقدّم خدمات إدارة متكاملة للتطبيقات والمواقع.':'We provide end-to-end management for apps and websites.'; ?></p>
    </div>
    <div class="grid-4">
      <?php foreach($H['services'] as $svc): ?>
        <a class="card fade-up" href="/service.php?slug=<?php echo esc($svc['slug']); ?>&lang=<?php echo esc($lang); ?>">
          <div class="icon"><?php echo esc($svc['icon']); ?></div>
          <h3><?php echo esc(t($svc['title'])); ?></h3>
          <p><?php echo esc(t($svc['desc'])); ?></p>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="mt-2 center fade-up"><a class="btn btn-muted" href="/services.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'استكشف جميع الخدمات':'Explore All Services'; ?></a></p>
  </div>
</section>
<?php endif; ?>

<?php if(!isset($D['show_process']) || $D['show_process'] === '1'): ?>
<section class="section" style="background: var(--bg-alt);">
  <div class="container">
    <div class="section-header fade-up">
      <h2><?php echo esc(t($H['processTitle'] ?? ['ar'=>'كيف نعمل','en'=>'How We Work','fr'=>'Notre Approche'])); ?></h2>
    </div>
    <div class="grid-3">
      <?php $i=1; foreach($H['process'] as $p): ?>
        <div class="card fade-up">
          <div class="icon"><?php echo $i; ?></div>
          <h3><?php echo esc(t($p['title'])); ?></h3>
          <p><?php echo esc(t($p['desc'])); ?></p>
        </div>
      <?php $i++; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(!isset($D['show_why']) || $D['show_why'] === '1'): ?>
<section class="section">
  <div class="container">
    <div class="section-header fade-up">
      <h2><?php echo esc(t($H['whyTitle'] ?? ['ar'=>'لماذا تختارنا؟','en'=>'Why Choose Us?','fr'=>'Pourquoi Nous ?'])); ?></h2>
    </div>
    <div class="grid-3">
      <?php foreach($H['why'] as $w): ?>
        <div class="card fade-up">
          <h3><?php echo esc(t($w['title'])); ?></h3>
          <p><?php echo esc(t($w['desc'])); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(!isset($D['show_final_cta']) || $D['show_final_cta'] === '1'): ?>
<section class="section" style="background: var(--bg-alt);">
  <div class="container center fade-up">
    <h2><?php echo esc(t($H['finalCTATitle'])); ?></h2>
    <p class="lead" style="margin-inline:auto;"><?php echo esc(t($H['finalCTADesc'])); ?></p>
    <p class="mt-2"><a class="btn btn-primary btn-lg" href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo esc(t($H['finalCTAButton'])); ?></a></p>
  </div>
</section>
<?php endif; ?>
<?php require __DIR__.'/templates/footer.php'; ?>
