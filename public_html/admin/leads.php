<?php include __DIR__.'/_header.php'; $db = leads_db(); csrf_verify(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['export'])){
  $out = fopen('php://output', 'w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=leads.csv');
  fputcsv($out, ['id','created_at','name','email','phone','company','website','service','budget','deadline','features','message','file_path','status']);
  foreach($db->query('SELECT * FROM leads ORDER BY id DESC') as $row){ fputcsv($out, $row); }
  exit;
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['status_id'])){
  $id = (int)$_POST['status_id']; $st = $_POST['status'] ?? 'new';
  $stmt = $db->prepare('UPDATE leads SET status=? WHERE id=?'); $stmt->execute([$st,$id]);
  $msg='تم تحديث الحالة';
}
?>
<h1>الطلبات الواردة</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" style="margin-bottom:12px"><?php csrf_field(); ?><button class="btn btn-outline" name="export" value="1">تصدير CSV</button></form>
<table class="table">
  <tr><th>#</th><th>الاسم</th><th>البريد</th><th>الخدمة</th><th>الميزانية</th><th>الحالة</th><th>إجراءات</th></tr>
  <?php foreach($db->query('SELECT * FROM leads ORDER BY id DESC') as $row): ?>
    <tr>
      <td><?php echo (int)$row['id']; ?></td>
      <td><?php echo esc($row['name']); ?></td>
      <td><?php echo esc($row['email']); ?></td>
      <td><?php echo esc($row['service']); ?></td>
      <td><?php echo esc($row['budget']); ?></td>
      <td><span class="status-badge"><?php echo esc($row['status']); ?></span></td>
      <td>
        <form method="post" style="display:inline">
          <?php csrf_field(); ?>
          <input type="hidden" name="status_id" value="<?php echo (int)$row['id']; ?>">
          <select name="status" class="input" style="display:inline-block;width:auto">
            <option <?php echo $row['status']=='new'?'selected':''; ?>>new</option>
            <option <?php echo $row['status']=='in_progress'?'selected':''; ?>>in_progress</option>
            <option <?php echo $row['status']=='done'?'selected':''; ?>>done</option>
          </select>
          <button class="btn btn-outline" type="submit">تحديث</button>
        </form>
      </td>
    </tr>
    <tr><td colspan="7"><strong>Message:</strong> <?php echo nl2br(esc($row['message'])); ?><?php if($row['file_path']): ?> — <a href="/<?php echo esc($row['file_path']); ?>">ملف</a><?php endif; ?></td></tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__.'/_footer.php'; ?>