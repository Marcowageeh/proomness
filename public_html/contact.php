<?php $page_key='contact'; require __DIR__.'/templates/header.php'; $S = get_settings();
$msg = ''; $ok = false;
if($_SERVER['REQUEST_METHOD']==='POST'){
  csrf_verify();
  $name = trim($_POST['name']??''); $email = trim($_POST['email']??''); $message = trim($_POST['message']??'');
  if(!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message){
    $msg = $lang==='ar'?'يرجى ملء جميع الحقول':'Please fill all fields';
  } else {
    $subject = ($lang==='ar'?'رسالة تواصل جديدة من ':'New contact message from ') . $name;
    $body = "Name: $name\nEmail: $email\nMessage:\n$message";
    send_email($S['contact_email'], $subject, $body);
    // Create admin notification
    create_notification(
      'contact',
      ($lang==='ar'?'رسالة تواصل جديدة':'New contact message'),
      $name . ' — ' . mb_substr($message, 0, 80),
      '/admin/leads.php'
    );
    $ok = true;
  }
}
?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'اتصل بنا':'Contact us'; ?></h1>
      <p class="lead"><?php echo $lang==='ar'?'نسعد بالتواصل معك':'We\'d love to hear from you'; ?></p>
    </div>
    <div class="grid-2 mt-2">
      <div class="card fade-up">
        <h3><?php echo $lang==='ar'?'القنوات الرسمية':'Official channels'; ?></h3>
        <p>✉️ <?php echo $lang==='ar'?'البريد':'Email'; ?>: <a href="mailto:<?php echo esc($S['contact_email']); ?>"><?php echo esc($S['contact_email']); ?></a></p>
        <p>📞 <?php echo $lang==='ar'?'الهاتف':'Phone'; ?>: <a href="tel:<?php echo esc($S['phone']); ?>"><?php echo esc($S['phone']); ?></a></p>
        <p>⏰ <?php echo esc(t($S['hours'])); ?></p>
        <p>📍 <?php echo $lang==='ar'?'العنوان':'Address'; ?>: <?php echo esc(t($S['address'])); ?></p>
        <hr>
        <h3><?php echo $lang==='ar'?'سياسة الاستجابة':'Response policy'; ?></h3>
        <p><?php echo $lang==='ar'?'نرد خلال يوم عمل واحد على الاستفسارات العامة.':'We reply within one business day.'; ?></p>
        <p><a class="btn btn-primary" href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'أرسل طلب مشروع':'Send project brief'; ?></a></p>
      </div>
      <div class="card fade-up">
        <h3><?php echo $lang==='ar'?'راسلنا':'Send a message'; ?></h3>
        <?php if($ok): ?>
          <div style="padding:1rem;background:var(--accent);color:#fff;border-radius:var(--radius)">
            <?php echo $lang==='ar'?'تم إرسال رسالتك بنجاح!':'Message sent successfully!'; ?>
          </div>
        <?php else: ?>
          <?php if($msg): ?><div style="padding:.75rem;background:#fee;border-radius:var(--radius);margin-bottom:1rem"><?php echo esc($msg); ?></div><?php endif; ?>
          <form method="post" class="form">
            <?php csrf_field(); ?>
            <div><label><?php echo $lang==='ar'?'الاسم':'Name'; ?></label><input class="input" name="name" required></div>
            <div><label><?php echo $lang==='ar'?'البريد':'Email'; ?></label><input class="input" type="email" name="email" required></div>
            <div><label><?php echo $lang==='ar'?'الرسالة':'Message'; ?></label><textarea class="input" name="message" rows="5" required></textarea></div>
            <button class="btn btn-primary" type="submit"><?php echo $lang==='ar'?'إرسال':'Send'; ?></button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
