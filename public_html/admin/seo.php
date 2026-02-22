<?php include __DIR__.'/_header.php'; $SEO = load_json('seo.json'); $S=get_settings(); csrf_verify(); $msg='';
$pages = ['home','about','services','pricing','faq','contact','brief'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($pages as $p){
    foreach(($S['languages']??[]) as $lg){
      $SEO[$p]['title'][$lg] = $_POST["title_{$p}_$lg"] ?? ($SEO[$p]['title'][$lg] ?? '');
      $SEO[$p]['desc'][$lg]  = $_POST["desc_{$p}_$lg"] ?? ($SEO[$p]['desc'][$lg] ?? '');
    }
  }
  save_json('seo.json', $SEO);
  // regenerate sitemap.xml
  $domain = rtrim($S['domain'],'/'); if(!$domain) $domain = base_url();
  $paths = ['/index.php','/about.php','/services.php','/pricing.php','/faq.php','/contact.php','/brief.php'];
  $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
  foreach($paths as $p){ $xml .= "  <url><loc>{$domain}{$p}</loc></url>\n"; }
  $xml .= "</urlset>\n";
  file_put_contents(__DIR__.'/../sitemap.xml', $xml);
  $msg='تم الحفظ وتحديث الخريطة';
}
?>
<h1>SEO & Sitemap</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <?php foreach($pages as $p): ?>
    <div class="card">
      <h3>صفحة: <?php echo esc($p); ?></h3>
      <?php foreach(($S['languages']??[]) as $lg): ?>
        <h4>لغة: <?php echo strtoupper($lg); ?></h4>
        <label>العنوان</label><input class="input" name="title_<?php echo $p; ?>_<?php echo $lg; ?>" value="<?php echo esc($SEO[$p]['title'][$lg] ?? ''); ?>">
        <label>الوصف</label><textarea class="input" name="desc_<?php echo $p; ?>_<?php echo $lg; ?>" rows="2"><?php echo esc($SEO[$p]['desc'][$lg] ?? ''); ?></textarea>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>