<?php
$page_key = 'support';
require __DIR__.'/templates/header.php';
$D = get_design();
?>
<section class="section fade-up">
  <div class="container" style="max-width:700px;text-align:center">
    <div class="section-header">
      <h1><?php echo $lang==='ar' ? 'الدعم المباشر' : 'Live Support'; ?></h1>
      <p class="lead">
        <?php echo $lang==='ar' ? 'يمكنك التواصل معنا مباشرة عبر الدردشة أو البريد الإلكتروني.' : 'Reach us directly via live chat or email.'; ?>
      </p>
    </div>
    <?php if(!empty($D['support_script'])): ?>
      <?php echo $D['support_script']; ?>
    <?php else: ?>
      <div class="card fade-up" style="margin-top:2rem">
        <p style="font-size:3rem">💬</p>
        <h3><?php echo $lang==='ar' ? 'نحن هنا للمساعدة' : 'We\'re here to help'; ?></h3>
        <p><?php echo $lang==='ar' ? 'تواصل معنا من خلال صفحة الاتصال أو أرسل لنا بريد.' : 'Contact us via the contact page or send us an email.'; ?></p>
        <div style="display:flex;gap:1rem;justify-content:center;margin-top:1rem">
          <a class="btn btn-primary" href="/contact.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'تواصل معنا':'Contact us'; ?></a>
          <a class="btn btn-outline" href="mailto:<?php echo esc(get_settings()['contact_email']); ?>">✉️ <?php echo $lang==='ar'?'أرسل بريد':'Send email'; ?></a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>