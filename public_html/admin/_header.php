<?php require_once __DIR__ . '/../inc/functions.php'; $S = get_settings(); if(empty($noauth)) require_admin(); $lang = get_lang(); $_admin_email = $_SESSION['admin']['email'] ?? ''; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Proomnes — Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0}
    :root{
      --admin-bg:#f8fafc;--admin-surface:#fff;--admin-text:#0f172a;--admin-muted:#64748b;
      --admin-border:#e2e8f0;--admin-primary:#2563eb;--admin-primary-hover:#1d4ed8;
      --admin-accent:#10b981;--admin-sidebar-bg:#0f172a;--admin-sidebar-text:#cbd5e1;
      --admin-sidebar-active:#2563eb;--admin-radius:10px;--admin-shadow:0 1px 3px rgba(0,0,0,.08);
      --sidebar-width:260px;
    }
    body{font-family:'IBM Plex Sans Arabic',sans-serif;background:var(--admin-bg);color:var(--admin-text);line-height:1.6;font-size:14px}
    a{color:var(--admin-primary);text-decoration:none}
    .admin-wrap{display:flex;min-height:100vh}
    .admin-sidebar{width:var(--sidebar-width);background:var(--admin-sidebar-bg);color:var(--admin-sidebar-text);padding:1.5rem 0;position:fixed;top:0;bottom:0;overflow-y:auto;z-index:100;transition:transform .3s}
    .admin-sidebar .brand{padding:0 1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:1rem}
    .admin-sidebar .brand h3{color:#fff;font-size:1.1rem;margin:0}
    .admin-sidebar .brand small{color:var(--admin-muted);font-size:.75rem}
    .admin-sidebar nav{display:flex;flex-direction:column;gap:2px;padding:0 .75rem}
    .admin-sidebar nav a{display:flex;align-items:center;gap:.6rem;padding:.55rem 1rem;border-radius:8px;color:var(--admin-sidebar-text);font-size:.85rem;transition:background .15s,color .15s}
    .admin-sidebar nav a:hover{background:rgba(255,255,255,.06);color:#fff}
    .admin-sidebar nav a.active{background:var(--admin-sidebar-active);color:#fff;font-weight:600}
    .admin-sidebar nav .nav-group{font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;color:var(--admin-muted);padding:.75rem 1rem .25rem;margin-top:.5rem}
    .admin-content{margin-right:var(--sidebar-width);flex:1;padding:2rem 2.5rem;min-width:0}
    .admin-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;padding-bottom:1rem;border-bottom:1px solid var(--admin-border)}
    .admin-topbar h1{font-size:1.4rem;font-weight:700;margin:0}
    .admin-topbar .meta{color:var(--admin-muted);font-size:.8rem}
    /* Cards & grids */
    .stat-card{background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:var(--admin-radius);padding:1.25rem 1.5rem;box-shadow:var(--admin-shadow)}
    .stat-card h4{color:var(--admin-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.25rem}
    .stat-card .value{font-size:1.75rem;font-weight:700;color:var(--admin-text)}
    .grid-stats{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:2rem}
    .card{background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:var(--admin-radius);padding:1.5rem;box-shadow:var(--admin-shadow);margin-bottom:1rem}
    .card h3{margin:0 0 .75rem;font-size:1rem}
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
    .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
    .form label{display:block;margin:.75rem 0 .3rem;font-weight:500;font-size:.85rem;color:var(--admin-text)}
    .input,.form select,.form textarea{width:100%;padding:.55rem .75rem;border:1px solid var(--admin-border);border-radius:8px;font-size:.85rem;font-family:inherit;background:var(--admin-surface);transition:border-color .15s}
    .input:focus,.form select:focus,.form textarea:focus{outline:none;border-color:var(--admin-primary);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
    .btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.15rem;border:none;border-radius:8px;font-family:inherit;font-size:.85rem;font-weight:600;cursor:pointer;transition:background .15s,transform .1s}
    .btn:active{transform:scale(.97)}
    .btn-primary{background:var(--admin-primary);color:#fff}.btn-primary:hover{background:var(--admin-primary-hover)}
    .btn-outline{background:transparent;color:var(--admin-primary);border:1px solid var(--admin-border)}.btn-outline:hover{border-color:var(--admin-primary);background:rgba(37,99,235,.04)}
    .btn-danger{background:#ef4444;color:#fff}.btn-danger:hover{background:#dc2626}
    .btn-sm{padding:.35rem .75rem;font-size:.78rem}
    table{width:100%;border-collapse:collapse;font-size:.85rem}
    th,td{text-align:right;padding:.6rem .75rem;border-bottom:1px solid var(--admin-border)}
    th{font-weight:600;color:var(--admin-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;background:var(--admin-bg)}
    tr:hover td{background:rgba(37,99,235,.02)}
    .badge{display:inline-block;padding:.15rem .5rem;border-radius:20px;font-size:.72rem;font-weight:600}
    .badge-new{background:#dbeafe;color:#2563eb}.badge-done{background:#d1fae5;color:#059669}.badge-progress{background:#fef3c7;color:#92400e}
    .mt-0{margin-top:0}.mt-1{margin-top:.5rem}.mt-2{margin-top:1rem}
    .menu-toggle{display:none;background:var(--admin-primary);color:#fff;border:none;padding:.5rem .75rem;border-radius:8px;font-size:1.1rem;cursor:pointer;position:fixed;top:1rem;right:1rem;z-index:200}
    /* ── Notification Bell ── */
    .notif-bell-wrap{position:fixed;top:1rem;left:1rem;z-index:200}
    .notif-bell{background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:50%;width:42px;height:42px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;cursor:pointer;position:relative;box-shadow:var(--admin-shadow);transition:transform .15s}
    .notif-bell:hover{transform:scale(1.08)}
    .notif-badge{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;min-width:18px;height:18px;border-radius:9px;display:none;align-items:center;justify-content:center;padding:0 4px;animation:notifPulse 2s infinite}
    @keyframes notifPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}
    /* ── Notification Panel ── */
    .notif-panel{display:none;position:fixed;top:60px;left:1rem;width:360px;max-height:480px;background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:var(--admin-radius);box-shadow:0 8px 30px rgba(0,0,0,.15);z-index:201;overflow:hidden}
    .notif-panel-header{display:flex;justify-content:space-between;align-items:center;padding:.75rem 1rem;border-bottom:1px solid var(--admin-border);background:var(--admin-bg)}
    .notif-panel-header h4{margin:0;font-size:.85rem}
    .notif-panel-header button{background:none;border:none;color:var(--admin-primary);font-size:.75rem;cursor:pointer;font-weight:600;font-family:inherit}
    .notif-list{overflow-y:auto;max-height:380px;padding:0}
    .notif-item{display:flex;gap:.6rem;padding:.75rem 1rem;border-bottom:1px solid var(--admin-border);text-decoration:none;color:var(--admin-text);transition:background .1s}
    .notif-item:hover{background:rgba(37,99,235,.04)}
    .notif-unread{background:rgba(37,99,235,.06);border-right:3px solid var(--admin-primary)}
    .notif-item-icon{font-size:1.2rem;flex-shrink:0;margin-top:2px}
    .notif-item-body{flex:1;min-width:0}
    .notif-item-title{font-size:.8rem;font-weight:600;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .notif-item-text{font-size:.72rem;color:var(--admin-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .notif-item-time{font-size:.65rem;color:var(--admin-muted);margin-top:3px}
    .notif-empty{text-align:center;padding:2rem 1rem;color:var(--admin-muted);font-size:.85rem}
    /* ── Permission Banner ── */
    .notif-perm-btn{display:none;position:fixed;bottom:1rem;left:1rem;z-index:200;background:var(--admin-primary);color:#fff;border:none;padding:.6rem 1.2rem;border-radius:8px;font-family:inherit;font-size:.8rem;font-weight:600;cursor:pointer;box-shadow:0 4px 15px rgba(37,99,235,.3);animation:notifSlideUp .4s ease}
    @keyframes notifSlideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    /* ── Toast Notifications ── */
    .notif-toast-container{position:fixed;bottom:1rem;right:1rem;z-index:300;display:flex;flex-direction:column-reverse;gap:.5rem;max-width:380px;pointer-events:none}
    .notif-toast{pointer-events:auto;display:flex;align-items:flex-start;gap:.6rem;padding:.75rem 1rem;background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:10px;box-shadow:0 8px 25px rgba(0,0,0,.12);animation:toastIn .3s ease;min-width:280px}
    .notif-toast-lead{border-right:3px solid var(--admin-primary)}
    .notif-toast-contact{border-right:3px solid var(--admin-accent)}
    .notif-toast-chat{border-right:3px solid #f59e0b}
    .notif-toast-info{border-right:3px solid var(--admin-muted)}
    .notif-toast-icon{font-size:1.3rem;flex-shrink:0}
    .notif-toast-content{flex:1;min-width:0}
    .notif-toast-content strong{font-size:.82rem;display:block;margin-bottom:2px}
    .notif-toast-content p{font-size:.72rem;color:var(--admin-muted);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .notif-toast-close{background:none;border:none;font-size:1.1rem;color:var(--admin-muted);cursor:pointer;padding:0;line-height:1}
    .notif-toast-exit{animation:toastOut .3s ease forwards}
    @keyframes toastIn{from{transform:translateX(30px);opacity:0}to{transform:translateX(0);opacity:1}}
    @keyframes toastOut{from{opacity:1}to{opacity:0;transform:translateX(30px)}}
    /* ── Full-Screen Flash ── */
    .notif-flash{position:fixed;inset:0;z-index:9999;pointer-events:none;display:none;background:radial-gradient(circle at center,rgba(239,68,68,.35),rgba(239,68,68,.15),transparent 70%)}
    @keyframes notifFlashAnim{0%{opacity:1}25%{opacity:.3}50%{opacity:.9}75%{opacity:.2}100%{opacity:0}}
    /* ── Page Shake ── */
    @keyframes notifShake{0%,100%{transform:translateX(0)}10%,50%,90%{transform:translateX(-5px)}30%,70%{transform:translateX(5px)}}
    .notif-shake{animation:notifShake .6s ease}
    @media(max-width:900px){
      .admin-sidebar{transform:translateX(100%)}
      .admin-sidebar.open{transform:translateX(0)}
      .admin-content{margin-right:0;padding:1.5rem 1rem;padding-top:4rem}
      .menu-toggle{display:block}
      .grid-2,.grid-3{grid-template-columns:1fr}
      .notif-panel{left:.5rem;right:.5rem;width:auto}
      .notif-toast-container{left:.5rem;right:.5rem;max-width:none}
    }
  </style>
  <script src="/assets/js/admin.js" defer></script>
  <script src="/assets/js/admin-notifications.js" defer></script>
  <script>window.VAPID_PUBLIC_KEY = '<?php echo get_vapid_keys()["public_key"]; ?>';</script>
</head>
<body>
<button class="menu-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('open')" aria-label="Menu">&#9776;</button>

<!-- Notification Bell -->
<div class="notif-bell-wrap">
  <div class="notif-bell" id="notifBell" onclick="toggleNotifPanel()" title="الإشعارات">
    🔔
    <span class="notif-badge" id="notifBadge"></span>
  </div>
</div>

<!-- Notification Dropdown Panel -->
<div class="notif-panel" id="notifPanel">
  <div class="notif-panel-header">
    <h4>🔔 الإشعارات</h4>
    <button onclick="markAllNotifRead()">تعيين الكل كمقروء</button>
  </div>
  <div class="notif-list" id="notifList">
    <div class="notif-empty" id="notifEmpty">لا توجد إشعارات جديدة</div>
  </div>
</div>

<!-- Toast Container -->
<div class="notif-toast-container" id="toastContainer"></div>

<!-- Flash Overlay -->
<div class="notif-flash" id="notifFlash"></div>

<!-- Permission Button -->
<button class="notif-perm-btn" id="notifPermBtn" onclick="Notification.requestPermission().then(function(p){if(p==='granted')this.style.display='none'}.bind(this))">
  🔔 تفعيل الإشعارات الفورية
</button>
<script>
if('Notification' in window && Notification.permission==='default'){
  document.getElementById('notifPermBtn').style.display='block';
}
</script>

<div class="admin-wrap">
  <aside class="admin-sidebar">
    <div class="brand">
      <h3>Proomnes</h3>
      <small><?php echo esc($_admin_email); ?></small>
    </div>
    <nav>
      <div class="nav-group"><?php echo $lang==='ar'?'عام':'General'; ?></div>
      <a href="/admin/dashboard.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='dashboard.php'?'class="active"':''; ?>>📊 <?php echo $lang==='ar'?'لوحة التحكم':'Dashboard'; ?></a>
      <a href="/admin/settings.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='settings.php'?'class="active"':''; ?>>⚙️ <?php echo $lang==='ar'?'الإعدادات':'Settings'; ?></a>
      <a href="/admin/users.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='users.php'?'class="active"':''; ?>>👥 <?php echo $lang==='ar'?'المستخدمون':'Users'; ?></a>

      <div class="nav-group"><?php echo $lang==='ar'?'المحتوى':'Content'; ?></div>
      <a href="/admin/services.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='services.php'?'class="active"':''; ?>>🛠️ <?php echo $lang==='ar'?'الخدمات':'Services'; ?></a>
      <a href="/admin/posts.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='posts.php'?'class="active"':''; ?>>📝 <?php echo $lang==='ar'?'المدونة':'Blog'; ?></a>
      <a href="/admin/categories.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='categories.php'?'class="active"':''; ?>>📂 <?php echo $lang==='ar'?'التصنيفات':'Categories'; ?></a>
      <a href="/admin/plans.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='plans.php'?'class="active"':''; ?>>💎 <?php echo $lang==='ar'?'الباقات':'Plans'; ?></a>
      <a href="/admin/faq.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='faq.php'?'class="active"':''; ?>>❓ <?php echo $lang==='ar'?'الأسئلة الشائعة':'FAQ'; ?></a>

      <div class="nav-group"><?php echo $lang==='ar'?'التسويق':'Marketing'; ?></div>
      <a href="/admin/seo.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='seo.php'?'class="active"':''; ?>>🔍 SEO</a>
      <a href="/admin/leads.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='leads.php'?'class="active"':''; ?>>📋 <?php echo $lang==='ar'?'الطلبات':'Leads'; ?></a>
      <a href="/admin/broadcast.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='broadcast.php'?'class="active"':''; ?>>📢 <?php echo $lang==='ar'?'إشعارات العملاء':'Client Notifications'; ?></a>

      <div class="nav-group"><?php echo $lang==='ar'?'النظام':'System'; ?></div>
      <a href="/admin/design.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='design.php'?'class="active"':''; ?>>🎨 <?php echo $lang==='ar'?'التصميم':'Design'; ?></a>
      <a href="/admin/payments.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='payments.php'?'class="active"':''; ?>>💳 <?php echo $lang==='ar'?'الدفع':'Payments'; ?></a>
      <a href="/admin/integrations.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='integrations.php'?'class="active"':''; ?>>🔗 <?php echo $lang==='ar'?'التكاملات':'Integrations'; ?></a>
      <a href="/admin/ai_settings.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='ai_settings.php'?'class="active"':''; ?>>🤖 AI</a>
      <a href="/admin/hours.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='hours.php'?'class="active"':''; ?>>🕐 <?php echo $lang==='ar'?'ساعات العمل':'Hours'; ?></a>
      <a href="/admin/radio.php" <?php echo basename($_SERVER['SCRIPT_NAME'])==='radio.php'?'class="active"':''; ?>>📻 <?php echo $lang==='ar'?'الراديو':'Radio'; ?></a>
      <hr style="border-color:rgba(255,255,255,.08);margin:1rem .5rem">
      <a href="/admin/logout.php" style="color:#f87171">🚪 <?php echo $lang==='ar'?'خروج':'Logout'; ?></a>
    </nav>
  </aside>
  <main class="admin-content">
