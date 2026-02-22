<?php $page_key='pricing'; require __DIR__.'/templates/header.php'; ?>
<section class="section">
  <div class="container center">
    <h1>⚠️ <?php echo $lang==='ar'?'تم إلغاء العملية':'Payment cancelled'; ?></h1>
    <p><?php echo $lang==='ar'?'يمكنك المحاولة مرة أخرى أو اختيار طريقة أخرى.':'You may try again or choose a different method.'; ?></p>
    <p class="muted"><?php echo $lang==='ar'?'سيتم تحويلك خلال 5 ثوانٍ...':'Redirecting in 5 seconds...'; ?></p>
    <script>setTimeout(function(){location.href='/contact.php?lang=<?php echo esc($lang); ?>';},5000);</script>
    <p><a class="btn btn-primary" href="/payment.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'العودة للدفع':'Back to payment'; ?></a></p>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
