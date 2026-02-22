<?php
$page_key='blog';
require __DIR__.'/inc/functions.php';
$db = site_db(); $S=get_settings(); $lang=get_lang();
$slug = $_GET['slug'] ?? '';
$stmt=$db->prepare("SELECT * FROM posts WHERE slug=? AND status='published'");
$stmt->execute([$slug]);
$p=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$p){ http_response_code(404); $title='Not found'; $desc=''; } else { $title = post_lang($p,'title'); $desc = post_lang($p,'excerpt'); }
$custom_seo = $p ? true : false;
if($custom_seo){
  $seo_title = $title;
  $seo_desc = $desc;
  $seo_image = $p['cover'] ?: ($S['logos']['og']??'assets/images/logo2.png');
}
require __DIR__.'/templates/header.php'; ?>
<section class="section">
  <div class="container">
    <?php if(!$p): ?>
      <h1>404</h1><p><?php echo $lang==='ar'?'المقال غير موجود':'Post not found'; ?></p>
    <?php else: ?>
      <div class="breadcrumbs"><a href="/blog.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'المدونة':'Blog'; ?></a> / <span><?php echo esc($title); ?></span></div>
      <h1 class="mt-0"><?php echo esc($title); ?></h1>
      <p class="lead"><?php echo esc($desc); ?></p>
      <?php if($p['cover']): ?><img src="/<?php echo esc($p['cover']); ?>" alt="" style="border-radius:12px;margin:10px 0"><?php endif; ?>
      <article><?php echo nl2br(esc(post_lang($p,'content'))); ?></article>
      <script type="application/ld+json">
      <?php
      $ld = [
        "@context"=>"https://schema.org","@type"=>"BlogPosting",
        "headline"=>$title,"description"=>$desc,"image"=>url($p['cover'] ?: ($S['logos']['og']??'assets/images/logo2.png')),
        "datePublished"=>$p['published_at'] ?: $p['created_at'], "dateModified"=>$p['updated_at'] ?: $p['created_at'],
        "author"=>["@type"=>"Organization","name"=>$S['site_name']],"publisher"=>["@type"=>"Organization","name"=>$S['site_name'],"logo"=>["@type"=>"ImageObject","url"=>url($S['logos']['primary'])]]
      ];
      echo json_encode($ld, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
      ?>
      </script>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__.'/templates/footer.php'; ?>
