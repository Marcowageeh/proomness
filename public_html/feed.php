<?php
require __DIR__.'/inc/functions.php'; $db = site_db(); header("Content-Type: application/rss+xml; charset=UTF-8");
$S = get_settings(); $lang = get_lang();
$items = $db->query("SELECT * FROM posts WHERE status='published' ORDER BY COALESCE(published_at,created_at) DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<rss version="2.0">
  <channel>
    <title><?php echo esc($S['site_name']); ?> — Blog</title>
    <link><?php echo esc(base_url()); ?></link>
    <description><?php echo esc(t($S['slogan'])); ?></description>
    <?php foreach($items as $p): $title = post_lang($p,'title'); $desc = post_lang($p,'excerpt'); ?>
      <item>
        <title><?php echo esc($title); ?></title>
        <link><?php echo esc(url('post.php?slug='.$p['slug'].'&lang='.$lang)); ?></link>
        <description><?php echo esc($desc); ?></description>
        <pubDate><?php echo esc($p['published_at'] ?: $p['created_at']); ?></pubDate>
        <guid><?php echo esc(url('post.php?slug='.$p['slug'])); ?></guid>
      </item>
    <?php endforeach; ?>
  </channel>
</rss>
