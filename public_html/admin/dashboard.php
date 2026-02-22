<?php include __DIR__.'/_header.php'; $S = get_settings();
$db = leads_db(); 
$totalLeads = (int)$db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$newLeads = (int)$db->query("SELECT COUNT(*) FROM leads WHERE status='new'")->fetchColumn();
$sdb = site_db();
$totalPosts = (int)$sdb->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$publishedPosts = (int)$sdb->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$recentLeads = $db->query("SELECT id,name,email,created_at,status FROM leads ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-topbar">
  <div>
    <h1><?php echo $lang==='ar'?'لوحة التحكم':'Dashboard'; ?></h1>
    <span class="meta"><?php echo $lang==='ar'?'مرحبًا':'Welcome'; ?>, <?php echo esc($_admin_email); ?></span>
  </div>
  <a class="btn btn-primary btn-sm" href="/" target="_blank">🌐 <?php echo $lang==='ar'?'عرض الموقع':'View site'; ?></a>
</div>

<div class="grid-stats">
  <div class="stat-card"><h4><?php echo $lang==='ar'?'إجمالي الطلبات':'Total leads'; ?></h4><div class="value"><?php echo $totalLeads; ?></div></div>
  <div class="stat-card"><h4><?php echo $lang==='ar'?'طلبات جديدة':'New leads'; ?></h4><div class="value" style="color:var(--admin-primary)"><?php echo $newLeads; ?></div></div>
  <div class="stat-card"><h4><?php echo $lang==='ar'?'المقالات':'Posts'; ?></h4><div class="value"><?php echo $totalPosts; ?></div></div>
  <div class="stat-card"><h4><?php echo $lang==='ar'?'مقالات منشورة':'Published'; ?></h4><div class="value" style="color:var(--admin-accent)"><?php echo $publishedPosts; ?></div></div>
</div>

<div class="grid-2">
  <div class="card">
    <h3>📋 <?php echo $lang==='ar'?'آخر الطلبات':'Recent leads'; ?></h3>
    <?php if($recentLeads): ?>
    <table>
      <thead><tr><th>#</th><th><?php echo $lang==='ar'?'الاسم':'Name'; ?></th><th><?php echo $lang==='ar'?'البريد':'Email'; ?></th><th><?php echo $lang==='ar'?'الحالة':'Status'; ?></th></tr></thead>
      <tbody>
        <?php foreach($recentLeads as $l): ?>
        <tr>
          <td><?php echo $l['id']; ?></td>
          <td><?php echo esc($l['name']); ?></td>
          <td><?php echo esc($l['email']); ?></td>
          <td><span class="badge <?php echo $l['status']==='new'?'badge-new':($l['status']==='done'?'badge-done':'badge-progress'); ?>"><?php echo esc($l['status']); ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="mt-1"><a href="/admin/leads.php" class="btn btn-outline btn-sm"><?php echo $lang==='ar'?'عرض الكل':'View all'; ?> →</a></p>
    <?php else: ?>
    <p style="color:var(--admin-muted)"><?php echo $lang==='ar'?'لا توجد طلبات بعد':'No leads yet'; ?></p>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>⚡ <?php echo $lang==='ar'?'روابط سريعة':'Quick links'; ?></h3>
    <div style="display:flex;flex-direction:column;gap:.5rem;margin-top:.75rem">
      <a href="/admin/posts.php" class="btn btn-outline btn-sm">📝 <?php echo $lang==='ar'?'إضافة مقال':'New post'; ?></a>
      <a href="/admin/services.php" class="btn btn-outline btn-sm">🛠️ <?php echo $lang==='ar'?'إدارة الخدمات':'Manage services'; ?></a>
      <a href="/admin/design.php" class="btn btn-outline btn-sm">🎨 <?php echo $lang==='ar'?'تعديل التصميم':'Edit design'; ?></a>
      <a href="/admin/seo.php" class="btn btn-outline btn-sm">🔍 <?php echo $lang==='ar'?'تحسين SEO':'SEO settings'; ?></a>
      <a href="/admin/settings.php" class="btn btn-outline btn-sm">⚙️ <?php echo $lang==='ar'?'الإعدادات العامة':'General settings'; ?></a>
    </div>
  </div>
</div>

<div class="card mt-2">
  <h3>ℹ️ <?php echo $lang==='ar'?'معلومات النظام':'System info'; ?></h3>
  <table>
    <tr><td><strong><?php echo $lang==='ar'?'اسم الموقع':'Site name'; ?></strong></td><td><?php echo esc($S['site_name']); ?></td></tr>
    <tr><td><strong><?php echo $lang==='ar'?'النطاق':'Domain'; ?></strong></td><td><?php echo esc($S['domain']); ?></td></tr>
    <tr><td><strong><?php echo $lang==='ar'?'البريد':'Email'; ?></strong></td><td><?php echo esc($S['contact_email']); ?></td></tr>
    <tr><td><strong><?php echo $lang==='ar'?'اللغات':'Languages'; ?></strong></td><td><?php echo esc(implode(', ', $S['languages'] ?? [])); ?></td></tr>
    <tr><td><strong>PHP</strong></td><td><?php echo PHP_VERSION; ?></td></tr>
  </table>
</div>
<?php include __DIR__.'/_footer.php'; ?>