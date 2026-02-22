<?php $page_key='about'; require __DIR__.'/templates/header.php'; $H = load_home(); $S = get_settings(); ?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'من نحن':'About us'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'شريكك التقني الموثوق':'Your trusted technology partner'; ?></p>
    </div>
    <div class="grid-2 mt-2">
      <div class="fade-up">
        <h2>🎯 <?php echo $lang==='ar'?'رؤيتنا ومهمتنا':'Our vision & mission'; ?></h2>
        <p><?php echo $lang==='ar'?'أن نكون الشريك التقني الأفضل للشركات الناشئة والمتوسطة، ندير بنيتك الرقمية باحترافية حتى تتفرغ لنمو أعمالك.':'To be the go-to technical partner for startups and mid-size businesses — managing your digital infrastructure professionally so you can focus on growth.'; ?></p>
        <h2 class="mt-2">🏢 <?php echo $lang==='ar'?'عن الشركة':'About the company'; ?></h2>
        <p><?php echo $lang==='ar'?'Proomnes متخصصة في إدارة التطبيقات والمواقع الإلكترونية — عمليات موثوقة، استضافة مُدارة، أمن سيبراني، وتحسين أداء. فريقنا يعمل على مدار الساعة لضمان استقرار خدماتك الرقمية.':'Proomnes specializes in full-lifecycle app & website management — reliable operations, managed hosting, cybersecurity, and performance optimization. Our team works around the clock to keep your digital services running smoothly.'; ?></p>
      </div>
      <div class="card fade-up" style="text-align:center">
        <img src="/assets/images/logo1.png" alt="Proomnes" style="max-width:160px;margin:0 auto 1rem">
        <p class="lead" style="font-weight:700">Proomnes</p>
        <p style="color:var(--muted)"><?php echo esc(t($S['slogan'])); ?></p>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
