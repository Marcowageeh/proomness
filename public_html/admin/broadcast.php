<?php
require_once __DIR__ . '/_header.php';
require_admin();

$lang = get_lang();
$success = '';
$error = '';

// Handle broadcast submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])){
  csrf_verify();
  if($_POST['action'] === 'broadcast'){
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body'] ?? '');
    $link  = trim($_POST['link'] ?? '');
    if(!$title){
      $error = $lang==='ar'?'عنوان الإشعار مطلوب':'Notification title is required';
    } else {
      $id = create_client_broadcast($title, $body, $link);
      $count = count_client_subscribers();
      $success = $lang==='ar'
        ? "تم إرسال الإشعار بنجاح إلى {$count} مشترك"
        : "Notification sent successfully to {$count} subscribers";
    }
  }
}

$subscribers = count_client_subscribers();
$broadcasts = get_client_broadcasts(20);
?>

<div class="admin-topbar">
  <div>
    <h1>📢 <?php echo $lang==='ar'?'إرسال إشعارات للعملاء':'Client Notifications'; ?></h1>
    <div class="meta"><?php echo $lang==='ar'?'أرسل إشعارات فورية لجميع زوار الموقع المشتركين':'Send instant push notifications to all subscribed website visitors'; ?></div>
  </div>
</div>

<?php if($success): ?>
<div style="background:#d1fae5;color:#065f46;padding:.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;border:1px solid #a7f3d0">
  ✅ <?php echo esc($success); ?>
</div>
<?php endif; ?>
<?php if($error): ?>
<div style="background:#fee2e2;color:#991b1b;padding:.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;border:1px solid #fecaca">
  ❌ <?php echo esc($error); ?>
</div>
<?php endif; ?>

<div class="grid-2">
  <!-- Broadcast Form -->
  <div class="card">
    <h3>📣 <?php echo $lang==='ar'?'إشعار جديد':'New Broadcast'; ?></h3>
    <form method="POST" class="form">
      <?php csrf_field(); ?>
      <input type="hidden" name="action" value="broadcast">

      <label><?php echo $lang==='ar'?'عنوان الإشعار':'Notification Title'; ?> *</label>
      <input type="text" name="title" class="input" required maxlength="200"
        placeholder="<?php echo $lang==='ar'?'مثال: عرض خاص — خصم 50% لفترة محدودة':'Example: Special offer — 50% off for limited time'; ?>">

      <label><?php echo $lang==='ar'?'نص الإشعار':'Notification Body'; ?></label>
      <textarea name="body" class="input" rows="3" maxlength="500"
        placeholder="<?php echo $lang==='ar'?'تفاصيل إضافية عن الإشعار...':'Additional details about the notification...'; ?>"></textarea>

      <label><?php echo $lang==='ar'?'رابط (اختياري)':'Link (optional)'; ?></label>
      <input type="url" name="link" class="input"
        placeholder="<?php echo $lang==='ar'?'https://proomnes.company/services.php':'https://proomnes.company/services.php'; ?>">

      <div style="margin-top:1rem;display:flex;align-items:center;gap:1rem">
        <button type="submit" class="btn btn-primary">
          🚀 <?php echo $lang==='ar'?'إرسال للجميع':'Send to All'; ?>
        </button>
        <span style="font-size:.78rem;color:var(--admin-muted)">
          👥 <?php echo $lang==='ar'?"$subscribers مشترك":"$subscribers subscribers"; ?>
        </span>
      </div>
    </form>
  </div>

  <!-- Stats & Info -->
  <div>
    <div class="stat-card" style="margin-bottom:1rem">
      <h4><?php echo $lang==='ar'?'المشتركون':'Subscribers'; ?></h4>
      <div class="value"><?php echo $subscribers; ?></div>
    </div>
    <div class="stat-card" style="margin-bottom:1rem">
      <h4><?php echo $lang==='ar'?'إجمالي الإشعارات المرسلة':'Total Broadcasts Sent'; ?></h4>
      <div class="value"><?php echo count($broadcasts); ?></div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(124,58,237,.06))">
      <h3 style="font-size:.85rem">💡 <?php echo $lang==='ar'?'نصائح':'Tips'; ?></h3>
      <ul style="font-size:.78rem;color:var(--admin-muted);padding-right:1.2rem;margin:0;line-height:1.8">
        <li><?php echo $lang==='ar'?'الإشعارات تصل حتى لو كان المتصفح مغلقاً':'Notifications are delivered even when the browser is closed'; ?></li>
        <li><?php echo $lang==='ar'?'استخدم عناوين قصيرة وجذابة':'Use short and compelling titles'; ?></li>
        <li><?php echo $lang==='ar'?'أضف رابطاً لتوجيه العميل مباشرةً':'Add a link to direct the client immediately'; ?></li>
        <li><?php echo $lang==='ar'?'لا تكثر من الإشعارات حتى لا يلغي العميل الاشتراك':'Don\'t over-notify to avoid unsubscriptions'; ?></li>
      </ul>
    </div>
  </div>
</div>

<!-- Recent Broadcasts -->
<?php if($broadcasts): ?>
<div class="card" style="margin-top:1.5rem">
  <h3>📋 <?php echo $lang==='ar'?'سجل الإشعارات المرسلة':'Broadcast History'; ?></h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th><?php echo $lang==='ar'?'العنوان':'Title'; ?></th>
        <th><?php echo $lang==='ar'?'النص':'Body'; ?></th>
        <th><?php echo $lang==='ar'?'الرابط':'Link'; ?></th>
        <th><?php echo $lang==='ar'?'التاريخ':'Date'; ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($broadcasts as $b): ?>
      <tr>
        <td><?php echo (int)$b['id']; ?></td>
        <td><?php echo esc($b['title']); ?></td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo esc($b['body']); ?></td>
        <td><?php echo $b['link'] ? '<a href="'.esc($b['link']).'" target="_blank">🔗</a>' : '—'; ?></td>
        <td style="white-space:nowrap;font-size:.75rem;color:var(--admin-muted)"><?php echo date('Y-m-d H:i', strtotime($b['created_at'])); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/_footer.php'; ?>
