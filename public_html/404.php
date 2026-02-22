<?php
$page_key = '404';
require __DIR__.'/templates/header.php';
?>
<section class="section">
  <div class="container center">
    <div class="empty-state fade-up">
      <div class="icon">🔍</div>
      <h1>404</h1>
      <h3><?php echo $lang==='ar'?'الصفحة غير موجودة':'Page Not Found'; ?></h3>
      <p class="lead" style="margin-inline:auto;"><?php echo $lang==='ar'?'عذرًا، الصفحة التي تبحث عنها غير متاحة أو تم نقلها.':'Sorry, the page you\'re looking for doesn\'t exist or has been moved.'; ?></p>
      <div class="mt-2">
        <a class="btn btn-primary btn-lg" href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'العودة للرئيسية':'Back to Home'; ?></a>
        <a class="btn btn-outline btn-lg" href="/contact.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'اتصل بنا':'Contact Us'; ?></a>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
