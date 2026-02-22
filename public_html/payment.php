<?php $page_key='pricing'; require __DIR__.'/templates/header.php'; $PL=load_plans(); $S=get_settings(); ?>
<section class="section">
  <div class="container">
    <div class="breadcrumbs"><a href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الرئيسية':'Home'; ?></a> / <span><?php echo $lang==='ar'?'الدفع الإلكتروني':'Online payment'; ?></span></div>
    <h1 class="mt-0"><?php echo $lang==='ar'?'الدفع الإلكتروني':'Online payment'; ?></h1>
    <p class="lead"><?php echo $lang==='ar'?'اختر باقتك وأكمل الدفع بشكل آمن.':'Choose a plan and complete a secure payment.'; ?></p>
    <div class="pricing mt-2">
      <?php foreach($PL as $p): $label = t($p['name']); ?>
        <div class="card plan <?php echo !empty($p['popular'])?'popular':''; ?>">
          <h3 class="mt-0"><?php echo esc($label); ?></h3>
          <div class="price"><?php echo esc($p['price']); ?><span style="font-size:14px"><?php echo esc(t($p['period'])); ?></span></div>
          <form method="post" action="/create-checkout-session.php">
            <?php csrf_field(); ?>
            <input type="hidden" name="plan_id" value="<?php echo esc($p['id']); ?>">
            <button class="btn <?php echo !empty($p['popular'])?'btn-primary':'btn-outline'; ?>" type="submit"><?php echo $lang==='ar'?'ادفع الآن':'Pay now'; ?></button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if(empty($S['payments']['stripe_secret'])): ?>
      <div class="card mt-2"><strong>ملاحظة:</strong> لم يتم إعداد Stripe بعد. ضع المفاتيح في <a href="/admin/payments.php">لوحة الدفع</a>.</div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
