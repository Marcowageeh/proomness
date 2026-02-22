<?php $page_key='pricing'; require __DIR__.'/templates/header.php'; ?>
<section class="section">
  <div class="container center">
    <h1>✅ <?php echo $lang==='ar'?'تم الدفع بنجاح':'Payment successful'; ?></h1>
    <p><?php echo $lang==='ar'?'شكرًا لاختيارك proomnes. ستصلك رسالة تأكيد.':'Thanks for choosing proomnes. You will receive a confirmation email.'; ?></p>
    <p><a class="btn btn-primary" href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'العودة للرئيسية':'Back to home'; ?></a></p>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
