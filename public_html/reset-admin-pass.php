<?php
/**
 * Password Reset Utility
 * Upload to server, run once, then DELETE immediately.
 * URL: https://yourdomain.com/reset-admin-pass.php?key=Proomnes2026!
 */
$SECRET_KEY = 'Proomnes2026!';

if (($_GET['key'] ?? '') !== $SECRET_KEY) {
    http_response_code(403);
    die('Forbidden');
}

$NEW_PASSWORD = 'Admin@2026';
$hash = password_hash($NEW_PASSWORD, PASSWORD_BCRYPT);

// Fix admin.json
$adminFile = __DIR__ . '/data/admin.json';
$admin = json_decode(file_get_contents($adminFile), true);
$admin['password_hash'] = $hash;
file_put_contents($adminFile, json_encode($admin, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// Fix users.json
$usersFile = __DIR__ . '/data/users.json';
$users = json_decode(file_get_contents($usersFile), true);
foreach ($users as &$u) {
    if ($u['role'] === 'admin') {
        $u['password_hash'] = $hash;
    }
}
unset($u);
file_put_contents($usersFile, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// Create SQLite databases if missing
$dataDir = __DIR__ . '/data';

// leads.sqlite
$db1 = new PDO('sqlite:' . $dataDir . '/leads.sqlite');
$db1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db1->exec("CREATE TABLE IF NOT EXISTS leads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created_at TEXT, name TEXT, email TEXT, phone TEXT, company TEXT, website TEXT,
    service TEXT, budget TEXT, deadline TEXT, features TEXT, message TEXT, file_path TEXT, status TEXT DEFAULT 'new'
)");

// site.sqlite
$db2 = new PDO('sqlite:' . $dataDir . '/site.sqlite');
$db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db2->exec("CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE,
    created_at TEXT, updated_at TEXT, published_at TEXT, status TEXT, cover TEXT,
    title_ar TEXT, title_en TEXT, title_fr TEXT,
    excerpt_ar TEXT, excerpt_en TEXT, excerpt_fr TEXT,
    content_ar TEXT, content_en TEXT, content_fr TEXT
)");
$db2->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT UNIQUE, name_ar TEXT, name_en TEXT, name_fr TEXT
)");
$db2->exec("CREATE TABLE IF NOT EXISTS post_categories (
    post_id INTEGER, category_id INTEGER, PRIMARY KEY(post_id, category_id)
)");

// notifications.sqlite
$db3 = new PDO('sqlite:' . $dataDir . '/notifications.sqlite');
$db3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db3->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, title TEXT, body TEXT, link TEXT,
    is_read INTEGER DEFAULT 0, created_at TEXT DEFAULT (datetime('now'))
)");
$db3->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT, endpoint TEXT UNIQUE, p256dh TEXT, auth TEXT, created_at TEXT DEFAULT (datetime('now'))
)");
$db3->exec("CREATE TABLE IF NOT EXISTS client_push_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT, endpoint TEXT UNIQUE, p256dh TEXT, auth TEXT, created_at TEXT DEFAULT (datetime('now'))
)");
$db3->exec("CREATE TABLE IF NOT EXISTS client_broadcasts (
    id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT, url TEXT,
    sent_count INTEGER DEFAULT 0, created_at TEXT DEFAULT (datetime('now'))
)");

echo "<!DOCTYPE html><html dir='rtl' lang='ar'><head><meta charset='utf-8'><title>Reset Done</title>
<style>body{font-family:sans-serif;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.box{background:#1e293b;border-radius:12px;padding:2rem;max-width:500px;width:90%}
h2{color:#22c55e;margin:0 0 1rem}p{margin:.5rem 0;font-size:.9rem}
.warn{background:#7f1d1d;color:#fca5a5;padding:.75rem 1rem;border-radius:8px;margin-top:1.5rem;font-weight:600}
</style></head><body><div class='box'>
<h2>✅ تم بنجاح!</h2>
<p><strong>البريد:</strong> {$admin['email']}</p>
<p><strong>كلمة المرور:</strong> {$NEW_PASSWORD}</p>
<p><strong>قواعد البيانات:</strong> تم إنشاؤها (leads.sqlite, site.sqlite, notifications.sqlite)</p>
<div class='warn'>⚠️ احذف هذا الملف فوراً من السيرفر بعد الاستخدام!</div>
</div></body></html>";
