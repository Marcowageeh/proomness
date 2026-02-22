<?php include __DIR__.'/_header.php'; csrf_verify(); $PL = load_plans(); $S = get_settings(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  // save all
  $new = [];
  foreach($PL as $idx=>$p){
    $id = $p['id'];
    $obj = ["id"=>$id, "price"=>$_POST["price_$id"] ?? $p['price'], "period"=>[],"name"=>[],"features"=>[],"popular"=>!empty($_POST["popular_$id"])];
    foreach(($S['languages']??[]) as $lg){
      $obj['name'][$lg] = $_POST["name_{$id}_$lg"] ?? ($p['name'][$lg] ?? '');
      $obj['period'][$lg] = $_POST["period_{$id}_$lg"] ?? ($p['period'][$lg] ?? '');
      $features = array_filter(array_map('trim', explode("\n", $_POST["features_{$id}_$lg"] ?? '')));
      for($i=0;$i<count($features);$i++){ $obj['features'][$i][$lg] = $features[$i]; }
    }
    $new[] = $obj;
  }
  save_json('plans.json', $new);
  $PL = $new; $msg='تم الحفظ';
}
?>
<h1>الباقات والأسعار</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <?php foreach($PL as $p): $id=$p['id']; ?>
    <div class="card">
      <h3><?php echo strtoupper($id); ?></h3>
      <label>السعر</label><input class="input" name="price_<?php echo esc($id); ?>" value="<?php echo esc($p['price']); ?>">
      <?php foreach(($S['languages']??[]) as $lg): ?>
        <h4>لغة: <?php echo strtoupper($lg); ?></h4>
        <label>الاسم</label><input class="input" name="name_<?php echo esc($id); ?>_<?php echo esc($lg); ?>" value="<?php echo esc($p['name'][$lg] ?? ''); ?>">
        <label>الفترة</label><input class="input" name="period_<?php echo esc($id); ?>_<?php echo esc($lg); ?>" value="<?php echo esc($p['period'][$lg] ?? ''); ?>">
        <label>المزايا (سطر لكل ميزة)</label>
        <textarea class="input" name="features_<?php echo esc($id); ?>_<?php echo esc($lg); ?>" rows="4"><?php $lines=[]; foreach($p['features'] as $it){ $lines[]=$it[$lg]??''; } echo esc(implode("\n",$lines)); ?></textarea>
      <?php endforeach; ?>
      <label><input type="checkbox" name="popular_<?php echo esc($id); ?>" <?php echo !empty($p['popular'])?'checked':''; ?>> خطة مميزة (popular)</label>
    </div>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>