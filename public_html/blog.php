<?php $page_key='blog'; require __DIR__.'/templates/header.php'; $db = site_db(); 
$lang = get_lang(); $cat = $_GET['cat'] ?? ''; $q = trim($_GET['q'] ?? ''); 
$per_page = 9; $page = max(1, (int)($_GET['page'] ?? 1)); $offset = ($page - 1) * $per_page;
$where = "WHERE status='published'"; $params=[];
if($q){ $where .= " AND (title_ar LIKE :q OR title_en LIKE :q OR title_fr LIKE :q OR excerpt_ar LIKE :q OR excerpt_en LIKE :q OR excerpt_fr LIKE :q)"; $params[':q'] = '%'.$q.'%'; }
$countSql = "SELECT COUNT(*) FROM posts $where";
$countStmt = $db->prepare($countSql); $countStmt->execute($params); $total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $per_page));
$sql = "SELECT * FROM posts $where ORDER BY COALESCE(published_at,created_at) DESC LIMIT $per_page OFFSET $offset";
$stmt = $db->prepare($sql); $stmt->execute($params); $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cats = $db->query("SELECT * FROM categories ORDER BY name_en")->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="section fade-up">
  <div class="container">
    <div class="section-header">
      <h1><?php echo $lang==='ar'?'المدونة والأخبار':'Blog & News'; ?></h1>
    </div>
    <form method="get" class="form" style="max-width:500px;margin:0 auto 2rem"><input type="hidden" name="lang" value="<?php echo esc($lang); ?>">
      <input class="input" name="q" value="<?php echo esc($q); ?>" placeholder="<?php echo $lang==='ar'?'ابحث...':'Search...'; ?>">
    </form>
    <div class="grid-3">
      <?php if($posts): ?>
        <?php foreach($posts as $p): ?>
          <a class="card fade-up" href="/post.php?slug=<?php echo esc($p['slug']); ?>&lang=<?php echo esc($lang); ?>">
            <?php if($p['cover']): ?><img src="/<?php echo esc($p['cover']); ?>" alt="<?php echo esc(post_lang($p,'title')); ?>" style="border-radius:var(--radius);margin-bottom:.75rem;width:100%"><?php endif; ?>
            <h3 class="mt-0"><?php echo esc(post_lang($p,'title')); ?></h3>
            <p><?php echo esc(post_lang($p,'excerpt')); ?></p>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column:1/-1;text-align:center;padding:3rem 0">
          <p style="font-size:3rem">📝</p>
          <h2><?php echo $lang==='ar'?'لا توجد مقالات بعد':'No posts yet'; ?></h2>
        </div>
      <?php endif; ?>
    </div>
    <?php if($totalPages > 1): ?>
    <nav class="pagination mt-2">
      <?php if($page > 1): ?><a class="btn btn-outline" href="?lang=<?php echo esc($lang); ?>&q=<?php echo esc($q); ?>&page=<?php echo $page-1; ?>">&laquo; <?php echo $lang==='ar'?'السابق':'Prev'; ?></a><?php endif; ?>
      <span class="pagination-info"><?php echo "$page / $totalPages"; ?></span>
      <?php if($page < $totalPages): ?><a class="btn btn-outline" href="?lang=<?php echo esc($lang); ?>&q=<?php echo esc($q); ?>&page=<?php echo $page+1; ?>"><?php echo $lang==='ar'?'التالي':'Next'; ?> &raquo;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
