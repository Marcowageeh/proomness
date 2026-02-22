<?php
// admin/hours.php – Manage business hours for multiple languages
require_once __DIR__.'/_header.php';
csrf_verify();
require_admin();

// Load current hours or initialize from settings languages
$HOURS = get_hours();
$S = get_settings();
$languages = $S['languages'] ?? ['ar','en','fr'];
$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Gather posted hours per language
  $newHours = [];
  foreach($languages as $lg){
    $newHours[$lg] = trim($_POST['hours_'.$lg] ?? '');
  }
  save_hours($newHours);
  $HOURS = $newHours;
  $msg = 'تم تحديث ساعات العمل بنجاح';
}
?>
<h1>إدارة ساعات العمل</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <?php foreach($languages as $lg): ?>
    <label>ساعات العمل (<?php echo strtoupper($lg); ?>)</label>
    <textarea class="input" name="hours_<?php echo esc($lg); ?>" rows="2"><?php echo esc($HOURS[$lg] ?? ''); ?></textarea>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>