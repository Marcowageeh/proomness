<?php include __DIR__.'/_header.php'; csrf_verify(); $S = get_settings(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $S['payments']['stripe_public'] = $_POST['stripe_public'] ?? $S['payments']['stripe_public'];
  $S['payments']['stripe_secret'] = $_POST['stripe_secret'] ?? $S['payments']['stripe_secret'];
  $S['payments']['currency'] = $_POST['currency'] ?? $S['payments']['currency'];
  $S['payments']['success_url'] = $_POST['success_url'] ?? $S['payments']['success_url'];
  $S['payments']['cancel_url'] = $_POST['cancel_url'] ?? $S['payments']['cancel_url'];
  $S['payments']['paypal_business'] = $_POST['paypal_business'] ?? $S['payments']['paypal_business'];
  save_json('settings.json', $S);
  $msg='تم الحفظ';
}
?>
<h1>الدفع الإلكتروني</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <h3>Stripe</h3>
  <label>Public Key</label><input class="input" name="stripe_public" value="<?php echo esc($S['payments']['stripe_public']); ?>">
  <label>Secret Key</label><input class="input" name="stripe_secret" value="<?php echo esc($S['payments']['stripe_secret']); ?>">
  <label>Currency</label><input class="input" name="currency" value="<?php echo esc($S['payments']['currency']); ?>">
  <label>Success URL</label><input class="input" name="success_url" value="<?php echo esc($S['payments']['success_url']); ?>">
  <label>Cancel URL</label><input class="input" name="cancel_url" value="<?php echo esc($S['payments']['cancel_url']); ?>">
  <h3>PayPal</h3>
  <label>Business Email</label><input class="input" name="paypal_business" value="<?php echo esc($S['payments']['paypal_business']); ?>">
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>