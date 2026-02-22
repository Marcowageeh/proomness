<?php include __DIR__.'/_header.php'; $db = site_db(); $S=get_settings(); csrf_verify(); $msg='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cats = $db->query("SELECT * FROM categories ORDER BY name_en")->fetchAll(PDO::FETCH_ASSOC);
$post = ["slug"=>"","status"=>"draft","cover"=>"","title_ar"=>"","title_en"=>"","title_fr"=>"","excerpt_ar"=>"","excerpt_en"=>"","excerpt_fr"=>"","content_ar"=>"","content_en"=>"","content_fr"=>"","published_at"=>""];
if($id){ $stmt=$db->prepare("SELECT * FROM posts WHERE id=?"); $stmt->execute([$id]); $row=$stmt->fetch(PDO::FETCH_ASSOC); if($row) $post=$row; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $slug = slugify($_POST['slug'] ?: $_POST['title_en'] ?: $_POST['title_ar']);
  $cover = $post['cover'];
  if(!empty($_FILES['cover']['name'])){
    $fn = basename($_FILES['cover']['name']); $safe = preg_replace('/[^a-zA-Z0-9_\.-]/','_', $fn);
    $target = UPLOAD_DIR . '/' . time() . '_' . $safe;
    if(move_uploaded_file($_FILES['cover']['tmp_name'], $target)){ $cover = 'assets/uploads/' . basename($target); }
  }
  $data = [
    'slug'=>$slug, 'status'=>$_POST['status'] ?? 'draft', 'cover'=>$cover,
    'title_ar'=>$_POST['title_ar'] ?? '', 'title_en'=>$_POST['title_en'] ?? '', 'title_fr'=>$_POST['title_fr'] ?? '',
    'excerpt_ar'=>$_POST['excerpt_ar'] ?? '', 'excerpt_en'=>$_POST['excerpt_en'] ?? '', 'excerpt_fr'=>$_POST['excerpt_fr'] ?? '',
    'content_ar'=>$_POST['content_ar'] ?? '', 'content_en'=>$_POST['content_en'] ?? '', 'content_fr'=>$_POST['content_fr'] ?? '',
    'published_at'=>$_POST['published_at'] ?? null, 'updated_at'=>gmdate('c'), 'created_at'=>$post['created_at'] ?: gmdate('c')
  ];
  if($id){
    $sql="UPDATE posts SET slug=:slug,status=:status,cover=:cover,title_ar=:title_ar,title_en=:title_en,title_fr=:title_fr,excerpt_ar=:excerpt_ar,excerpt_en=:excerpt_en,excerpt_fr=:excerpt_fr,content_ar=:content_ar,content_en=:content_en,content_fr=:content_fr,published_at=:published_at,updated_at=:updated_at WHERE id=$id";
    $db->prepare($sql)->execute($data);
  } else {
    $sql="INSERT INTO posts (slug,status,cover,title_ar,title_en,title_fr,excerpt_ar,excerpt_en,excerpt_fr,content_ar,content_en,content_fr,published_at,updated_at,created_at) VALUES (:slug,:status,:cover,:title_ar,:title_en,:title_fr,:excerpt_ar,:excerpt_en,:excerpt_fr,:content_ar,:content_en,:content_fr,:published_at,:updated_at,:created_at)";
    $db->prepare($sql)->execute($data); $id = $db->lastInsertId();
  }
  // categories
  $db->prepare("DELETE FROM post_categories WHERE post_id=?")->execute([$id]);
  if(!empty($_POST['cats']) && is_array($_POST['cats'])){
    $ins=$db->prepare("INSERT INTO post_categories (post_id,category_id) VALUES (?,?)");
    foreach($_POST['cats'] as $cid){ $ins->execute([$id,(int)$cid]); }
  }
  $msg='تم الحفظ';
}
$postcats = []; if($id){ foreach($db->query("SELECT category_id FROM post_categories WHERE post_id=$id") as $r){ $postcats[]=(int)$r['category_id']; } }
?>
<h1><?php echo $id?'تعديل مقال':'مقال جديد'; ?></h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" enctype="multipart/form-data">
  <?php csrf_field(); ?>
  <label>Slug</label><input class="input" name="slug" value="<?php echo esc($post['slug']); ?>">
  <label>الحالة</label>
  <select class="input" name="status">
    <option value="draft" <?php echo $post['status']=='draft'?'selected':''; ?>>مسودة</option>
    <option value="published" <?php echo $post['status']=='published'?'selected':''; ?>>منشور</option>
  </select>
  <label>صورة الغلاف</label><input class="input" type="file" name="cover"> <?php if($post['cover']): ?><a href="/<?php echo esc($post['cover']); ?>" target="_blank">عرض</a><?php endif; ?>
  <div class="grid-2">
    <div>
      <h3>AR</h3>
      <label>العنوان</label><input class="input" name="title_ar" value="<?php echo esc($post['title_ar']); ?>">
      <label>مقتطف</label><textarea class="input" name="excerpt_ar" rows="3"><?php echo esc($post['excerpt_ar']); ?></textarea>
      <label>المحتوى</label><textarea class="input" name="content_ar" rows="8"><?php echo esc($post['content_ar']); ?></textarea>
    </div>
    <div>
      <h3>EN/FR</h3>
      <label>Title (EN)</label><input class="input" name="title_en" value="<?php echo esc($post['title_en']); ?>">
      <label>Excerpt (EN)</label><textarea class="input" name="excerpt_en" rows="3"><?php echo esc($post['excerpt_en']); ?></textarea>
      <label>Content (EN)</label><textarea class="input" name="content_en" rows="8"><?php echo esc($post['content_en']); ?></textarea>
      <label>Titre (FR)</label><input class="input" name="title_fr" value="<?php echo esc($post['title_fr']); ?>">
      <label>Extrait (FR)</label><textarea class="input" name="excerpt_fr" rows="3"><?php echo esc($post['excerpt_fr']); ?></textarea>
      <label>Contenu (FR)</label><textarea class="input" name="content_fr" rows="8"><?php echo esc($post['content_fr']); ?></textarea>
    </div>
  </div>
  <label>التصنيفات</label>
  <div>
    <?php foreach($cats as $c): ?>
      <label><input type="checkbox" name="cats[]" value="<?php echo (int)$c['id']; ?>" <?php echo in_array((int)$c['id'],$postcats)?'checked':''; ?>> <?php echo esc($c['name_ar'] ?: $c['name_en']); ?></label>
    <?php endforeach; ?>
  </div>
  <label>تاريخ النشر (اختياري)</label><input class="input" name="published_at" value="<?php echo esc($post['published_at']); ?>" placeholder="YYYY-MM-DD">
  <button class="btn btn-primary" type="submit">حفظ</button>
  <?php if($id): ?><a class="btn btn-outline" href="/post.php?slug=<?php echo esc($post['slug']); ?>" target="_blank">عرض المقال</a><?php endif; ?>
</form>
<?php include __DIR__.'/_footer.php'; ?>