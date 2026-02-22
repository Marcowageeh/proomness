<?php $page_key='faq'; require __DIR__.'/templates/header.php'; $FAQ = load_faq(); ?>
<section class="section faq fade-up">
  <div class="container" style="max-width:800px">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'الأسئلة الشائعة':'Frequently Asked Questions'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'إجابات شاملة على أبرز استفساراتك':'Answers to your most common questions'; ?></p>
    </div>
    <?php foreach($FAQ as $i=>$it): ?>
      <details class="fade-up" <?php echo $i===0?'open':''; ?>><summary><?php echo esc(t($it['q'])); ?></summary><p><?php echo esc(t($it['a'])); ?></p></details>
    <?php endforeach; ?>
    <div class="mt-2 fade-up" style="text-align:center">
      <p><?php echo $lang==='ar'?'لم تجد إجابتك؟':'Didn\'t find your answer?'; ?></p>
      <a class="btn btn-primary" href="/contact.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'تواصل معنا':'Contact us'; ?></a>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
