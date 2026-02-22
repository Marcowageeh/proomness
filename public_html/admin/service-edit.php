<?php include __DIR__.'/_header.php'; $S = get_settings(); $SVC = load_services(); csrf_verify();
$slug = $_GET['slug'] ?? '';
$svc = $slug && isset($SVC[$slug]) ? $SVC[$slug] : ["slug"=>"","icon"=>"","title"=>[],"desc"=>[],"deliver"=>[],"flow"=>[],"results"=>[],"duration"=>[],"price"=>[]];
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nslug = trim($_POST['slug'] ?? '');
  if(!$nslug) $nslug = slugify($_POST['title_ar'] ?? '');
  $svc['slug'] = $nslug;
  $svc['icon'] = $_POST['icon'] ?? $svc['icon'];
  foreach(($S['languages']??['ar','en']) as $lg){
    $svc['title'][$lg] = $_POST['title_'.$lg] ?? '';
    $svc['desc'][$lg] = $_POST['desc_'.$lg] ?? '';
    $svc['duration'][$lg] = $_POST['duration_'.$lg] ?? '';
    $svc['price'][$lg] = $_POST['price_'.$lg] ?? '';
  }
  $svc['deliver'] = []; $svc['flow']=[]; $svc['results']=[];
  foreach(($S['languages']??['ar','en']) as $lg){
    $del = array_filter(array_map('trim', explode("\n", $_POST['deliver_'.$lg] ?? '')));
    $flo = array_filter(array_map('trim', explode("\n", $_POST['flow_'.$lg] ?? '')));
    $res = array_filter(array_map('trim', explode("\n", $_POST['results_'.$lg] ?? '')));
    for($i=0;$i<count($del);$i++){ $svc['deliver'][$i][$lg] = $del[$i]; }
    for($i=0;$i<count($flo);$i++){ $svc['flow'][$i][$lg] = $flo[$i]; }
    for($i=0;$i<count($res);$i++){ $svc['results'][$i][$lg] = $res[$i]; }
  }
  $SVC[$nslug] = $svc;
  if($slug && $slug !== $nslug){ unset($SVC[$slug]); }
  save_json('services.json', $SVC);
  $msg='تم الحفظ';
  $slug = $nslug;
}
?>
<h1><?php echo $slug?'تعديل خدمة':'إضافة خدمة'; ?></h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <label>Slug</label><input class="input" name="slug" value="<?php echo esc($svc['slug']); ?>">
  <label>أيقونة (إيموجي/نص)</label><input class="input" name="icon" value="<?php echo esc($svc['icon']); ?>">
  <?php foreach(($S['languages']??[]) as $lg): ?>
    <h3>لغة: <?php echo strtoupper($lg); ?></h3>
    <label>العنوان</label><input class="input" name="title_<?php echo esc($lg); ?>" value="<?php echo esc($svc['title'][$lg] ?? ''); ?>">
    <label>الوصف</label><textarea class="input" name="desc_<?php echo esc($lg); ?>" rows="3"><?php echo esc($svc['desc'][$lg] ?? ''); ?></textarea>
    <label>المدة</label><input class="input" name="duration_<?php echo esc($lg); ?>" value="<?php echo esc($svc['duration'][$lg] ?? ''); ?>">
    <label>السعر</label><input class="input" name="price_<?php echo esc($lg); ?>" value="<?php echo esc($svc['price'][$lg] ?? ''); ?>">
    <label>بنود العمل (سطر لكل بند)</label><textarea class="input" name="deliver_<?php echo esc($lg); ?>" rows="4"><?php 
      $lines=[]; foreach(($svc['deliver']??[]) as $it){ $lines[] = $it[$lg] ?? ''; } echo esc(implode("\n",$lines)); ?></textarea>
    <label>نسق العمل (سطر لكل بند)</label><textarea class="input" name="flow_<?php echo esc($lg); ?>" rows="4"><?php 
      $lines=[]; foreach(($svc['flow']??[]) as $it){ $lines[] = $it[$lg] ?? ''; } echo esc(implode("\n",$lines)); ?></textarea>
    <label>النتائج المتوقعة (سطر لكل بند)</label><textarea class="input" name="results_<?php echo esc($lg); ?>" rows="4"><?php 
      $lines=[]; foreach(($svc['results']??[]) as $it){ $lines[] = $it[$lg] ?? ''; } echo esc(implode("\n",$lines)); ?></textarea>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>