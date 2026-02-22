<?php
$page_key='works';
require __DIR__.'/templates/header.php';
$items = json_decode(@file_get_contents(__DIR__.'/data/works.json'), true) ?: [];
?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'أعمالنا':'Our Work'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'عينات من مشاريع وشعارات وصفحات هبوط':'A selection of projects, logos, and landing pages.'; ?></p>
    </div>
    <?php if($items): ?>
    <div class="grid-3 mt-2">
      <?php foreach($items as $w): ?>
        <a class="card fade-up" href="<?php echo esc($w['url'] ?? '#'); ?>" target="_blank" rel="noopener" style="text-align:center">
          <img src="<?php echo esc($w['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo esc($w['title'] ?? ''); ?>" style="border-radius:var(--radius);margin-bottom:.75rem;max-width:100%">
          <h3 class="mt-0"><?php echo esc($w['title'] ?? ''); ?></h3>
          <p><?php echo esc($w['desc'][$lang] ?? ($w['desc']['en'] ?? '')); ?></p>
        </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state fade-up mt-2" style="text-align:center;padding:3rem">
      <p style="font-size:3rem">🛠️</p>
      <h2><?php echo $lang==='ar'?'قريبًا':'Coming Soon'; ?></h2>
      <p><?php echo $lang==='ar'?'نعمل على إضافة مشاريعنا':'We\'re adding our projects portfolio soon.'; ?></p>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
