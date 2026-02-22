<?php include __DIR__.'/_header.php'; $db = site_db(); $S=get_settings(); csrf_verify(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['new'])){
    $slug = slugify($_POST['slug'] ?: $_POST['name_ar']);
    $stmt = $db->prepare("INSERT OR IGNORE INTO categories (slug,name_ar,name_en,name_fr) VALUES (?,?,?,?)");
    $stmt->execute([$slug,$_POST['name_ar'],$_POST['name_en'],$_POST['name_fr']]);
    $msg='تمت الإضافة';
  }
  if(isset($_POST['save'])){
    $id=(int)$_POST['id'];
    $stmt=$db->prepare("UPDATE categories SET slug=?,name_ar=?,name_en=?,name_fr=? WHERE id=?");
    $stmt->execute([slugify($_POST['slug']),$_POST['name_ar'],$_POST['name_en'],$_POST['name_fr'],$id]);
    $msg='تم الحفظ';
  }
  if(isset($_POST['delete'])){
    $id=(int)$_POST['id'];
    $db->prepare("DELETE FROM post_categories WHERE category_id=?")->execute([$id]);
    $db->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    $msg='تم الحذف';
  }
}
$cats = $db->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>تصنيفات المدونة</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<div class="grid-2">
  <div class="card">
    <h3>إضافة</h3>
    <form method="post" class="form">
      <?php csrf_field(); ?><input type="hidden" name="new" value="1">
      <label>Slug</label><input class="input" name="slug">
      <label>الاسم (AR)</label><input class="input" name="name_ar">
      <label>Name (EN)</label><input class="input" name="name_en">
      <label>Nom (FR)</label><input class="input" name="name_fr">
      <button class="btn btn-primary">حفظ</button>
    </form>
  </div>
  <div class="card">
    <h3>القائمة</h3>
    <table class="table">
      <tr><th>ID</th><th>Slug</th><th>AR</th><th>EN</th><th>FR</th><th>إجراءات</th></tr>
      <?php foreach($cats as $c): ?>
        <tr>
          <form method="post">
            <?php csrf_field(); ?>
            <td><?php echo (int)$c['id']; ?><input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>"></td>
            <td><input class="input" name="slug" value="<?php echo esc($c['slug']); ?>"></td>
            <td><input class="input" name="name_ar" value="<?php echo esc($c['name_ar']); ?>"></td>
            <td><input class="input" name="name_en" value="<?php echo esc($c['name_en']); ?>"></td>
            <td><input class="input" name="name_fr" value="<?php echo esc($c['name_fr']); ?>"></td>
            <td>
              <button class="btn btn-outline" name="save" value="1">حفظ</button>
              <button class="btn btn-outline" name="delete" value="1" onclick="return confirmAction('حذف؟')">حذف</button>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php include __DIR__.'/_footer.php'; ?>