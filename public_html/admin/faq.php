<?php include __DIR__.'/_header.php'; csrf_verify(); $FAQ = load_faq(); $S=get_settings(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  // build from posted arrays length
  $count = (int)($_POST['count'] ?? count($FAQ));
  $new=[];
  for($i=0;$i<$count;$i++){
    $item = ["q"=>[],"a"=>[]];
    foreach(($S['languages']??[]) as $lg){
      $item['q'][$lg] = $_POST["q_{$i}_$lg"] ?? '';
      $item['a'][$lg] = $_POST["a_{$i}_$lg"] ?? '';
    }
    if(trim(implode('', $item['q'])) !== '') $new[]=$item;
  }
  save_json('faq.json', $new);
  $FAQ = $new; $msg='تم الحفظ';
}
?>
<h1>الأسئلة الشائعة</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <input type="hidden" name="count" value="<?php echo max( count($FAQ), 5 ); ?>">
  <?php $rows = max(count($FAQ), 5); for($i=0;$i<$rows;$i++): $it = $FAQ[$i] ?? ["q"=>[],"a"=>[]]; ?>
    <div class="card">
      <h3>عنصر #<?php echo $i+1; ?></h3>
      <?php foreach(($S['languages']??[]) as $lg): ?>
        <h4>لغة: <?php echo strtoupper($lg); ?></h4>
        <label>السؤال</label><input class="input" name="q_<?php echo $i; ?>_<?php echo $lg; ?>" value="<?php echo esc($it['q'][$lg] ?? ''); ?>">
        <label>الإجابة</label><textarea class="input" name="a_<?php echo $i; ?>_<?php echo $lg; ?>" rows="3"><?php echo esc($it['a'][$lg] ?? ''); ?></textarea>
      <?php endforeach; ?>
    </div>
  <?php endfor; ?>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>