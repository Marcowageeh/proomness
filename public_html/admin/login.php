<?php
$noauth = true;
require_once __DIR__ . '/../inc/functions.php';
$A = admin_info();
if(empty($A['password_hash'])){ header('Location: /admin/install.php'); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  csrf_verify();
  if(admin_login($_POST['email']??'', $_POST['pass']??'')){
    header('Location: /admin/dashboard.php'); exit;
  } else {
    $msg = 'بيانات اعتماد غير صحيحة';
  }
}
?>
<?php include __DIR__.'/_header.php'; ?>
<h1>تسجيل الدخول</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" style="max-width:400px">
  <?php csrf_field(); ?>
  <label>البريد</label><input class="input" type="email" name="email" required>
  <label>كلمة المرور</label><input class="input" type="password" name="pass" required>
  <button class="btn btn-primary" type="submit">دخول</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>