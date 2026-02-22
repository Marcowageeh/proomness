<?php include __DIR__.'/_header.php'; $db = site_db(); csrf_verify(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_id'])){
  $id=(int)$_POST['delete_id']; $db->prepare("DELETE FROM post_categories WHERE post_id=?")->execute([$id]); $db->prepare("DELETE FROM posts WHERE id=?")->execute([$id]); $msg='تم الحذف';
}
$posts = $db->query("SELECT id,slug,title_ar,title_en,status,published_at FROM posts ORDER BY COALESCE(published_at,created_at) DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>المقالات/الأخبار</h1>
<p><a class="btn btn-primary" href="/admin/post-edit.php">مقال جديد</a> <a class="btn btn-outline" href="/admin/categories.php">التصنيفات</a></p>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<table class="table">
  <tr><th>#</th><th>Slug</th><th>العنوان (AR)</th><th>Title (EN)</th><th>الحالة</th><th>نُشر</th><th>إجراءات</th></tr>
  <?php foreach($posts as $p): ?>
    <tr>
      <td><?php echo (int)$p['id']; ?></td>
      <td><?php echo esc($p['slug']); ?></td>
      <td><?php echo esc($p['title_ar']); ?></td>
      <td><?php echo esc($p['title_en']); ?></td>
      <td><?php echo esc($p['status']); ?></td>
      <td><?php echo esc($p['published_at']); ?></td>
      <td>
        <a class="btn btn-outline" href="/admin/post-edit.php?id=<?php echo (int)$p['id']; ?>">تعديل</a>
        <form method="post" style="display:inline" onsubmit="return confirmAction('حذف المقال؟')">
          <?php csrf_field(); ?>
          <input type="hidden" name="delete_id" value="<?php echo (int)$p['id']; ?>">
          <button class="btn btn-outline" type="submit">حذف</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__.'/_footer.php'; ?>