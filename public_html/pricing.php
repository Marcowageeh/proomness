<?php $page_key='pricing'; require __DIR__.'/templates/header.php'; $PL = load_plans(); ?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'الباقات والأسعار':'Plans & Pricing'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'اختر الباقة المناسبة لاحتياجاتك':'Choose the right plan for your needs'; ?></p>
    </div>
    <div class="pricing mt-2">
      <?php foreach($PL as $i=>$p): ?>
        <div class="card plan fade-up <?php echo !empty($p['popular'])?'popular':'';?>">
          <h3 class="mt-0"><?php echo esc(t($p['name'])); ?></h3>
          <div class="price"><?php echo esc($p['price']); ?><span style="font-size:14px"><?php echo esc(t($p['period'])); ?></span></div>
          <ul class="features">
            <?php foreach($p['features'] as $f): ?><li>✓ <?php echo esc(t($f)); ?></li><?php endforeach; ?>
          </ul>
          <p class="mt-2"><a class="btn <?php echo !empty($p['popular'])?'btn-primary btn-lg':'btn-outline'; ?>" href="/brief.php?plan=<?php echo esc($p['id']); ?>&lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'اختر هذه الباقة':'Choose this plan'; ?></a></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
