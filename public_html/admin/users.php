<?php include __DIR__.'/_header.php'; csrf_verify(); $US = users(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['new_user'])){
  $email = trim($_POST['email']); $pass=$_POST['pass']; $role=$_POST['role'];
  if(filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass)>=8 && in_array($role,['admin','editor'])){
    $US[] = ['email'=>$email,'password_hash'=>password_hash($pass, PASSWORD_BCRYPT),'role'=>$role,'created'=>gmdate('c')];
    save_users($US); $msg='تم إضافة المستخدم';
  } else { $msg='تحقق من المدخلات'; }
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['del_email'])){
  $email = $_POST['del_email'];
  $US = array_values(array_filter($US, fn($u)=>$u['email']!==$email));
  save_users($US); $msg='تم حذف المستخدم';
}
?>
<h1>المستخدمون</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<div class="grid-2">
  <div class="card">
    <h3>إضافة مستخدم</h3>
    <form method="post" class="form">
      <?php csrf_field(); ?>
      <input type="hidden" name="new_user" value="1">
      <label>البريد</label><input class="input" name="email" type="email" required>
      <label>كلمة المرور (8+)</label><input class="input" name="pass" type="password" required>
      <label>الصلاحية</label>
      <select class="input" name="role">
        <option value="editor">Editor</option>
        <option value="admin">Admin</option>
      </select>
      <button class="btn btn-primary" type="submit">حفظ</button>
    </form>
  </div>
  <div class="card">
    <h3>القائمة</h3>
    <table class="table">
      <tr><th>البريد</th><th>الدور</th><th>أُنشئ</th><th>إجراءات</th></tr>
      <?php foreach($US as $u): ?>
        <tr>
          <td><?php echo esc($u['email']); ?></td>
          <td><?php echo esc($u['role']); ?></td>
          <td><?php echo esc($u['created']); ?></td>
          <td>
            <form method="post" onsubmit="return confirmAction('حذف المستخدم؟')" style="display:inline">
              <?php csrf_field(); ?>
              <input type="hidden" name="del_email" value="<?php echo esc($u['email']); ?>">
              <button class="btn btn-outline" type="submit">حذف</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php include __DIR__.'/_footer.php'; ?>