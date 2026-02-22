<?php include __DIR__.'/_header.php'; $SVC = load_services(); $H = load_home(); csrf_verify(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reorder'])){
  // reorder using posted order (comma-separated slugs) for home services only
  $order = array_filter(explode(',', $_POST['order'] ?? ''));
  $list = $H['services'];
  $new = [];
  $bySlug = []; foreach($list as $it){ $bySlug[$it['slug']] = $it; }
  foreach($order as $slug){ if(isset($bySlug[$slug])) $new[] = $bySlug[$slug]; }
  foreach($list as $it){ if(!in_array($it['slug'], $order)) $new[] = $it; }
  $H['services'] = $new;
  save_json('home.json', $H);
  $msg='تم تحديث الترتيب';
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_slug'])){
  $slug = $_POST['delete_slug'];
  unset($SVC[$slug]);
  save_json('services.json', $SVC);
  $msg='تم حذف الخدمة';
}
?>
<h1>الخدمات</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<p><a class="btn btn-primary" href="/admin/service-edit.php">إضافة خدمة جديدة</a></p>
<table class="table">
  <tr><th>Slug</th><th>العنوان</th><th>أيقونة</th><th>إجراءات</th></tr>
  <?php foreach($SVC as $slug=>$sv): ?>
    <tr>
      <td><?php echo esc($slug); ?></td>
      <td><?php echo esc(t($sv['title'])); ?></td>
      <td><?php echo esc($sv['icon']); ?></td>
      <td>
        <a class="btn btn-outline" href="/admin/service-edit.php?slug=<?php echo esc($slug); ?>">تعديل</a>
        <form method="post" style="display:inline" onsubmit="return confirmAction('حذف الخدمة؟')">
          <?php csrf_field(); ?>
          <input type="hidden" name="delete_slug" value="<?php echo esc($slug); ?>">
          <button class="btn btn-outline" type="submit">حذف</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<h3>ترتيب بطاقات الخدمات في الصفحة الرئيسية</h3>
<form method="post">
  <?php csrf_field(); ?>
  <input type="hidden" name="reorder" value="1">
  <input class="input" name="order" value="<?php echo esc(implode(',', array_map(fn($x)=>$x['slug'],$H['services']))); ?>">
  <small class="note">أدخل الـslug بالترتيب مفصولًا بفواصل، مثل: application-management,website-management,...</small>
  <button class="btn btn-primary" type="submit">حفظ الترتيب</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>