<?php $page_key='services'; require __DIR__.'/templates/header.php'; $H = load_home(); $SERV = load_services(); ?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'خدماتنا':'Our Services'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'اطّلع على خدماتنا التفصيلية':'Browse our comprehensive services'; ?></p>
    </div>
    <div class="grid-3 mt-2">
      <?php foreach($H['services'] as $svc): ?>
        <a class="card scale-in" href="/service.php?slug=<?php echo esc($svc['slug']); ?>&lang=<?php echo esc($lang); ?>">
          <div style="font-size:2rem;margin-bottom:.5rem"><?php echo $svc['icon'] ?? '⚙️'; ?></div>
          <h3 class="mt-0"><?php echo esc(t($svc['title'])); ?></h3>
          <p><?php echo esc(t($svc['desc'])); ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
