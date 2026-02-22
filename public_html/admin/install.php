<?php
$noauth = true;
require_once __DIR__ . '/../inc/functions.php';
$A = admin_info();
if(!empty($A['password_hash'])){ header('Location: /admin/login.php'); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  csrf_verify();
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['pass'] ?? '';
  if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8){
    $msg = 'بريد غير صالح أو كلمة مرور قصيرة (8 على الأقل)';
  } else {
    $A['email'] = $email;
    $A['password_hash'] = password_hash($pass, PASSWORD_BCRYPT);
    save_json('admin.json', $A);
    header('Location: /admin/login.php'); exit;
  }
}
?>
<?php include __DIR__.'/_header.php'; ?>
<h1>تثبيت الإدارة</h1>
<p>عيّن بريد وكلمة مرور المسؤول لأول مرة.</p>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" style="max-width:480px">
  <?php csrf_field(); ?>
  <label>البريد</label><input class="input" type="email" name="email" required>
  <label>كلمة المرور</label><input class="input" type="password" name="pass" required>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>