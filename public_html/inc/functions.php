<?php
// inc/functions.php — helpers + RBAC + blog + integrations + payments
// Initialize the session with secure cookie parameters. We set the cookie
// attributes to improve security: HTTPOnly prevents client-side scripts from
// accessing the session cookie; Secure ensures cookies are only sent over HTTPS;
// SameSite=Lax mitigates CSRF attacks by restricting cross-site requests.
if (session_status() === PHP_SESSION_NONE) {
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
  // Set cookie params only once before session_start
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}
date_default_timezone_set('UTC');

define('DATA_DIR', __DIR__ . '/../data');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('IMG_DIR', __DIR__ . '/../assets/images');

function load_json($file){
  $path = DATA_DIR . '/' . $file;
  if(!file_exists($path)) return [];
  $s = file_get_contents($path);
  $j = json_decode($s, true);
  return $j ? $j : [];
}
function save_json($file, $data){
  $path = DATA_DIR . '/' . $file;
  $tmp = $path.'.tmp';
  file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  rename($tmp, $path);
  return true;
}
function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function base_url(){
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] == 443);
  $scheme = $https ? 'https://' : 'http://';
  return $scheme . $_SERVER['HTTP_HOST'];
}
function url($path=''){ return base_url() . '/' . ltrim($path,'/'); }
function current_url(){ return base_url() . $_SERVER['REQUEST_URI']; }

function slugify($text){
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = strtolower($text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  if (empty($text)) { return 'n-a'; }
  return $text;
}

// Settings & language
function get_settings(){ static $S=null; if($S===null){ $S=load_json('settings.json'); } return $S; }
function get_lang(){
  $S = get_settings();
  if(isset($_GET['lang']) && $_GET['lang']!=''){
    $_SESSION['lang'] = $_GET['lang'];
  }
  $lang = $_SESSION['lang'] ?? $S['default_lang'] ?? 'ar';
  if(!in_array($lang, $S['languages'])) $lang = $S['default_lang'] ?? 'ar';
  return $lang;
}
function t($arr){
  $lang = get_lang();
  if(is_array($arr)){
    if(isset($arr[$lang]) && $arr[$lang]) return $arr[$lang];
    if(isset($arr['en']) && $arr['en']) return $arr['en'];
    if(isset($arr['ar']) && $arr['ar']) return $arr['ar'];
    foreach($arr as $v){ if($v) return $v; }
    return '';
  }
  return $arr;
}

// SEO helpers
function seo_meta($page_key){
  $S = get_settings();
  $SEO = load_json('seo.json');
  $item = $SEO[$page_key] ?? null;
  $title = $item ? t($item['title']) : esc($S['site_name']);
  $desc  = $item ? t($item['desc'])  : t($S['slogan'] ?? []);
  $lang = get_lang();
  $canonical = strtok(current_url(), '?');
  echo '<title>'.esc($title).'</title>'."\n";
  echo '<meta name="description" content="'.esc($desc).'">'."\n";
  echo '<link rel="canonical" href="'.esc($canonical).'">'."\n";
  foreach(($S['languages'] ?? []) as $lg){
    $href = $canonical . (strpos($canonical,'?')===false ? '?' : '&') . 'lang='.$lg;
    echo '<link rel="alternate" hreflang="'.esc($lg).'" href="'.esc($href).'">'."\n";
  }
  echo '<meta property="og:locale" content="'.($lang==='ar'?'ar_AR':'en_US').'">'."\n";
  echo '<meta property="og:type" content="website">'."\n";
  echo '<meta property="og:title" content="'.esc($title).'">'."\n";
  echo '<meta property="og:description" content="'.esc($desc).'">'."\n";
  echo '<meta property="og:image" content="'.esc(url($S['logos']['og'] ?? 'assets/images/logo2.png')).'">'."\n";
  echo '<meta property="og:url" content="'.esc($canonical).'">'."\n";
  echo '<meta name="twitter:card" content="summary_large_image">'."\n";
  echo '<meta name="twitter:title" content="'.esc($title).'">'."\n";
  echo '<meta name="twitter:description" content="'.esc($desc).'">'."\n";
  echo '<meta name="twitter:image" content="'.esc(url($S['logos']['og'] ?? 'assets/images/logo2.png')).'">'."\n";
  // Organization JSON-LD
  $org = [
    "@context"=>"https://schema.org","@type"=>"Organization","name"=>$S['site_name'],
    "url"=>base_url(),"logo"=>url($S['logos']['primary'] ?? 'assets/images/logo1.png'),
    "contactPoint"=>[["@type"=>"ContactPoint","contactType"=>"customer support","email"=>$S['contact_email'],"telephone"=>$S['phone']]],
  ];
  echo '<script type="application/ld+json">'.json_encode($org, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
}
function seo_meta_custom($title,$desc,$image=null,$url_override=null){
  $S = get_settings();
  $title = $title ?: $S['site_name'];
  $desc  = $desc  ?: t($S['slogan'] ?? []);
  $urlv  = $url_override ?: strtok(current_url(), '?');
  $img   = $image ? url($image) : url($S['logos']['og'] ?? 'assets/images/logo2.png');
  echo '<title>'.esc($title).'</title>'."\n";
  echo '<meta name="description" content="'.esc($desc).'">'."\n";
  echo '<link rel="canonical" href="'.esc($urlv).'">'."\n";
  echo '<meta property="og:type" content="article">'."\n";
  echo '<meta property="og:title" content="'.esc($title).'">'."\n";
  echo '<meta property="og:description" content="'.esc($desc).'">'."\n";
  echo '<meta property="og:image" content="'.esc($img).'">'."\n";
  echo '<meta property="og:url" content="'.esc($urlv).'">'."\n";
  echo '<meta name="twitter:card" content="summary_large_image">'."\n";
  echo '<meta name="twitter:title" content="'.esc($title).'">'."\n";
  echo '<meta name="twitter:description" content="'.esc($desc).'">'."\n";
  echo '<meta name="twitter:image" content="'.esc($img).'">'."\n";
}

// Data loaders
function load_home(){ return load_json('home.json'); }
function load_services(){ return load_json('services.json'); }
function load_plans(){ return load_json('plans.json'); }
function load_faq(){ return load_json('faq.json'); }

// Working hours
function get_hours(){ static $H=null; if($H===null){ $H = load_json('hours.json'); } return $H; }
function save_hours($data){ save_json('hours.json', $data); return true; }

// OpenAI settings
function get_ai_settings(){ static $AI=null; if($AI===null){ $AI = load_json('ai.json'); } return $AI; }
function save_ai_settings($data){ save_json('ai.json', $data); return true; }

// CSRF
function csrf_token(){
  if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['csrf'];
}
function csrf_field(){ echo '<input type="hidden" name="csrf" value="'.esc(csrf_token()).'">'; }
function csrf_verify(){
  if($_SERVER['REQUEST_METHOD']==='POST'){
    if(empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])){
      http_response_code(403); die('Invalid CSRF token');
    }
  }
}

// SQLite: leads + blog
function leads_db(){
  $db = new PDO('sqlite:' . DATA_DIR . '/leads.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("CREATE TABLE IF NOT EXISTS leads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created_at TEXT,
    name TEXT, email TEXT, phone TEXT, company TEXT, website TEXT,
    service TEXT, budget TEXT, deadline TEXT,
    features TEXT, message TEXT, file_path TEXT, status TEXT DEFAULT 'new'
  )");
  return $db;
}
function site_db(){
  $db = new PDO('sqlite:' . DATA_DIR . '/site.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT UNIQUE,
    created_at TEXT, updated_at TEXT, published_at TEXT,
    status TEXT, cover TEXT,
    title_ar TEXT, title_en TEXT, title_fr TEXT,
    excerpt_ar TEXT, excerpt_en TEXT, excerpt_fr TEXT,
    content_ar TEXT, content_en TEXT, content_fr TEXT
  )");
  $db->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT UNIQUE, name_ar TEXT, name_en TEXT, name_fr TEXT
  )");
  $db->exec("CREATE TABLE IF NOT EXISTS post_categories (
    post_id INTEGER, category_id INTEGER, PRIMARY KEY(post_id, category_id)
  )");
  return $db;
}
function post_lang($row, $field){
  $lang = get_lang();
  $k = $field.'_'.$lang;
  if(isset($row[$k]) && $row[$k]) return $row[$k];
  if(isset($row[$field.'_en']) && $row[$field.'_en']) return $row[$field.'_en'];
  foreach(['ar','en','fr'] as $lg){ $kk=$field.'_'.$lg; if(isset($row[$kk]) && $row[$kk]) return $row[$kk]; }
  return '';
}

// Leads save + mail
function save_lead($data){
  $db = leads_db();
  $stmt = $db->prepare('INSERT INTO leads (created_at,name,email,phone,company,website,service,budget,deadline,features,message,file_path) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
  $stmt->execute([gmdate('c'),$data['name'],$data['email'],$data['phone'],$data['company'],$data['website'],$data['service'],$data['budget'],$data['deadline'],$data['features'],$data['message'],$data['file_path']]);
  return $db->lastInsertId();
}
function send_email($to,$subject,$body){
  $headers = "MIME-Version: 1.0\r\nContent-type:text/plain; charset=UTF-8\r\n";
  @mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, $headers);
  file_put_contents(DATA_DIR.'/mail.log', "TO: $to\nSUBJECT: $subject\n$body\n---\n", FILE_APPEND);
}

// Admin auth with roles
function users(){ $u = load_json('users.json'); if(!$u){ $a = load_json('admin.json'); if(!empty($a['password_hash'])){ $u = [ ["email"=>$a['email'],"password_hash"=>$a['password_hash'],"role"=>"admin","created"=>$a['created']??gmdate('c')] ]; save_json('users.json',$u);} } return $u; }
function save_users($arr){ return save_json('users.json',$arr); }
function admin_logged(){ return !empty($_SESSION['admin']); }
function require_role($roles=['admin','editor']){
  if(!admin_logged()){ header('Location: /admin/login.php'); exit; }
  $role = $_SESSION['admin']['role'] ?? 'admin';
  if(!in_array($role, $roles)){ http_response_code(403); die('Forbidden'); }
}
function require_admin(){ require_role(['admin','editor']); }
function admin_login($email,$pass){
  $A = load_json('admin.json'); // legacy
  if(!empty($A['password_hash']) && strtolower(trim($A['email']))===strtolower(trim($email)) && password_verify($pass, $A['password_hash'])){
    $_SESSION['admin'] = ['email'=>$A['email'],'role'=>'admin']; return true;
  }
  foreach(users() as $u){
    if(strtolower(trim($u['email']))===strtolower(trim($email)) && password_verify($pass, $u['password_hash'])){
      $_SESSION['admin'] = ['email'=>$u['email'],'role'=>$u['role'] ?? 'editor']; return true;
    }
  }
  return false;
}
function admin_logout(){ unset($_SESSION['admin']); }

// Integrations (CRM/Webhooks)
function integration_push($payload){
  $S = get_settings(); $I = $S['integrations'] ?? [];
  // webhook
  if(!empty($I['webhook_url'])){
    @integration_http_post($I['webhook_url'], $payload);
  }
  // HubSpot (Forms API) if token + portal + form guid set
  if(!empty($I['hubspot_token']) && !empty($I['hubspot_portal_id']) && !empty($I['hubspot_form_guid'])){
    $url = "https://api.hsforms.com/submissions/v3/integration/submit/{$I['hubspot_portal_id']}/{$I['hubspot_form_guid']}";
    @integration_http_post($url, [
      'fields'=>[
        ['name'=>'email','value'=>$payload['email']??''],
        ['name'=>'firstname','value'=>$payload['name']??''],
        ['name'=>'phone','value'=>$payload['phone']??''],
        ['name'=>'company','value'=>$payload['company']??''],
        ['name'=>'website','value'=>$payload['website']??''],
        ['name'=>'service','value'=>$payload['service']??''],
        ['name'=>'budget','value'=>$payload['budget']??''],
        ['name'=>'message','value'=>$payload['message']??'']
      ],
      'context'=>['pageUri'=>current_url(),'pageName'=>'brief']
    ], ['Content-Type: application/json', 'Authorization: Bearer '.$I['hubspot_token']]);
  }
  // Zoho (example via webhook if access token provided, minimal)
  if(!empty($I['zoho_access_token'])){
    $zurl = "https://www.zohoapis.com/crm/v2/Leads";
    $data = ['data'=>[ [ 'Company'=>$payload['company']??'N/A','Last_Name'=>$payload['name']??'Client','Email'=>$payload['email']??'','Phone'=>$payload['phone']??'','Description'=>$payload['message']??'' ] ]];
    @integration_http_post($zurl, $data, ['Authorization: Zoho-oauthtoken '.$I['zoho_access_token'], 'Content-Type: application/json']);
  }
}
function integration_http_post($url,$data,$headers=['Content-Type: application/json']){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($data)?$data:json_encode($data));
  $res = curl_exec($ch);
  curl_close($ch);
  file_put_contents(DATA_DIR.'/integrations.log', "POST $url\n".(is_string($data)?$data:json_encode($data))."\nRESP:$res\n---\n", FILE_APPEND);
}

// Payments (Stripe Checkout minimal via cURL)
function stripe_enabled(){ $S=get_settings(); return !empty($S['payments']['stripe_secret']); }
function stripe_create_checkout($name,$amount_cents,$currency='usd',$success_url='',$cancel_url=''){
  $S = get_settings(); $sk = $S['payments']['stripe_secret'] ?? ''; if(!$sk) return null;
  $success = $success_url ?: ($S['payments']['success_url'] ?? url('/success.php'));
  $cancel  = $cancel_url  ?: ($S['payments']['cancel_url'] ?? url('/cancel.php'));
  $post = [
    'mode'=>'payment',
    'success_url'=>$success.'?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'=>$cancel,
    'line_items[0][price_data][currency]'=>$currency ?: ($S['payments']['currency'] ?? 'usd'),
    'line_items[0][price_data][product_data][name]'=>$name,
    'line_items[0][price_data][unit_amount]'=>$amount_cents,
    'line_items[0][quantity]'=>1
  ];
  $ch = curl_init("https://api.stripe.com/v1/checkout/sessions");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
    CURLOPT_USERPWD=>$sk.':',
    CURLOPT_POSTFIELDS=>http_build_query($post)
  ]);
  $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
  $data = json_decode($resp, true);
  if($code>=200 && $code<300 && !empty($data['url'])) return $data['url'];
  file_put_contents(DATA_DIR.'/payments.log', "Stripe error [$code]: $resp\n", FILE_APPEND);
  return null;
}
// ======== Theme / Design Helpers ========
// Load design settings from JSON. These values control theme colors and hero image.
function get_design(){
  static $D = null;
  if($D === null){
    $D = load_json('design.json');
  }
  return $D;
}

// Output CSS variables based on design settings. This helper prints a <style> tag that
// overrides the default CSS variables defined in assets/css/styles.css. These variables
// control the primary color palette and accent colours used throughout the site. If a
// variable is missing in design.json, the default value from the stylesheet is used.
function design_css(){
  $D = get_design();
  $css = '';
  if(!empty($D)){
    $css .= ':root{' ;
    if(!empty($D['primary_color'])){
      $css .= '--primary: '.esc($D['primary_color']).';';
    }
    if(!empty($D['primary_color2'])){
      $css .= '--primary-2: '.esc($D['primary_color2']).';';
    }
    if(!empty($D['accent_color'])){
      $css .= '--accent: '.esc($D['accent_color']).';';
    }
    // Additional typographic and styling variables
    if(!empty($D['font_size_base'])){
      $css .= '--font-size-base: '.esc($D['font_size_base']).';';
    }
    if(!empty($D['font_size_heading'])){
      $css .= '--font-size-heading: '.esc($D['font_size_heading']).';';
    }
    if(!empty($D['image_opacity'])){
      $css .= '--image-opacity: '.esc($D['image_opacity']).';';
    }
    if(!empty($D['card_radius'])){
      $css .= '--card-radius: '.esc($D['card_radius']).';';
    }
    if(!empty($D['card_shadow'])){
      $css .= '--card-shadow: '.esc($D['card_shadow']).';';
    }
    // Surface/background opacity to modulate overlay transparency
    if(!empty($D['bg_opacity'])){
      $css .= '--bg-opacity: '.esc($D['bg_opacity']).';';
    }
    $css .= '}';
  }
  // Additional element-level overrides for typography, card styling and image opacity
  if(!empty($D['font_size_base'])){
    $css .= 'body{font-size: '.esc($D['font_size_base']).';}';
  }
  if(!empty($D['font_size_heading'])){
    // Set the main hero heading size and scale secondary headings to 75% of the defined size
    $heading = esc($D['font_size_heading']);
    $css .= '.hero h1{font-size: '.$heading.' !important;}';
    // For section headings (h2), reduce the size slightly for hierarchy
    // If numeric units provided (e.g. px or rem), multiply by 0.7 using calc
    $css .= '.section h2{font-size: calc('.$heading.' * 0.7) !important;}';
  }
  if(isset($D['image_opacity']) && $D['image_opacity'] !== ''){
    $css .= '.hero .visual img{opacity: '.esc($D['image_opacity']).';}';
  }
  if(!empty($D['card_radius'])){
    $css .= '.card{border-radius: '.esc($D['card_radius']).' !important;}';
  }
  if(!empty($D['card_shadow'])){
    $css .= '.card{box-shadow: '.esc($D['card_shadow']).' !important;}';
  }

    // Override text colours for dark and light themes if specified in design settings. If these values are provided
    // the corresponding CSS variables will be adjusted dynamically. This allows administrators to define custom
    // text colours for both themes via the dashboard. Only set the variable if a value is provided.
    // When a custom text colour is provided for dark or light themes, override both the
    // `--text` and `--muted` variables. The muted colour controls paragraph text, hints
    // and other secondary text elements. Without overriding `--muted`, only headings
    // would change colour when selecting a new text colour. By updating both
    // variables together we ensure that all text across the site – headings,
    // paragraphs, form labels and other components – reflects the chosen colour.
    if(!empty($D['dark_text_color'])){
      $clr = esc($D['dark_text_color']);
      $css .= 'body[data-theme="dark"]{--text: '.$clr.';--muted: '.$clr.';}';
    }
    if(!empty($D['light_text_color'])){
      $clr = esc($D['light_text_color']);
      $css .= 'body[data-theme="light"]{--text: '.$clr.';--muted: '.$clr.';}';
    }

    // Override card/section background colours for dark and light themes if specified. When administrators
    // choose a custom surface colour, update both `--surface` and `--surface-2` variables so that
    // headers, footers, cards, forms and other containers adopt the selected colour. Leaving these
    // values empty retains the defaults provided by the active theme.
    if(!empty($D['dark_surface_color'])){
      $clr = esc($D['dark_surface_color']);
      $css .= 'body[data-theme="dark"]{--surface: '.$clr.';--surface-2: '.$clr.';}';
    }
    if(!empty($D['light_surface_color'])){
      $clr = esc($D['light_surface_color']);
      $css .= 'body[data-theme="light"]{--surface: '.$clr.';--surface-2: '.$clr.';}';
    }
  if($css){
    echo '<style>'.$css.'</style>';
  }
}

/**
 * Output a <link> tag for the currently selected theme. Themes are stored in
 * public_html/assets/themes/ and defined in design.json under the "template"
 * key. If no template is defined the default theme is used. This helper is
 * called from templates/header.php.
 */
function design_theme_link(){
  $D = get_design();
  $template = $D['template'] ?? 'theme-default';
  // Sanitize template name to prevent directory traversal
  $template = preg_replace('/[^a-zA-Z0-9_\-]/','', $template);
  $href = '/assets/themes/'.$template.'.css';
  echo '<link rel="stylesheet" href="'.esc($href).'">';
}

/**
 * Output a <link> tag for importing the selected Google Font defined in
 * design.json. The font URL should point to a Google Fonts CSS URL. If
 * undefined the default Cairo font will be used.
 */
function design_font_import(){
  $D = get_design();
  if(!empty($D['font_url'])){
    echo '<link href="'.esc($D['font_url']).'" rel="stylesheet">';
  }
}

/**
 * Output a <style> tag that sets the body font-family based on the selected
 * font in design.json. Defaults to Cairo if unspecified.
 */
function design_font_style(){
  $D = get_design();
  $font = $D['font'] ?? 'Cairo';
  echo '<style>body{font-family: "'.esc($font).'", sans-serif;}</style>';
}
// ========== Base64URL Utilities ==========
function base64url_encode($data){ return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }
function base64url_decode($data){ return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4)); }

// ========== Notifications System ==========
function notif_db(){
  $db = new PDO('sqlite:' . DATA_DIR . '/notifications.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    title TEXT NOT NULL,
    body TEXT,
    link TEXT,
    is_read INTEGER DEFAULT 0,
    created_at TEXT NOT NULL
  )");
  $db->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    endpoint TEXT UNIQUE NOT NULL,
    p256dh TEXT,
    auth TEXT,
    created_at TEXT NOT NULL
  )");
  // Client (visitor) push subscriptions — separate from admin
  $db->exec("CREATE TABLE IF NOT EXISTS client_push_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    endpoint TEXT UNIQUE NOT NULL,
    p256dh TEXT,
    auth TEXT,
    created_at TEXT NOT NULL
  )");
  // Client broadcasts — messages sent from admin to all clients
  $db->exec("CREATE TABLE IF NOT EXISTS client_broadcasts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    body TEXT,
    link TEXT,
    created_at TEXT NOT NULL
  )");
  return $db;
}

function create_notification($type, $title, $body = '', $link = ''){
  $db = notif_db();
  $stmt = $db->prepare('INSERT INTO notifications (type, title, body, link, created_at) VALUES (?, ?, ?, ?, ?)');
  $stmt->execute([$type, $title, $body, $link, gmdate('c')]);
  $id = $db->lastInsertId();
  // Send Web Push to all subscribers (works even when browser is closed)
  try { broadcast_push(); } catch(Exception $e){
    file_put_contents(DATA_DIR.'/push.log', gmdate('c')." broadcast err: ".$e->getMessage()."\n", FILE_APPEND);
  }
  return $id;
}

function get_notifications_since($since_id = 0, $limit = 30){
  $db = notif_db();
  if($since_id > 0){
    $stmt = $db->prepare('SELECT * FROM notifications WHERE id > ? ORDER BY id DESC LIMIT ?');
    $stmt->execute([$since_id, $limit]);
  } else {
    $stmt = $db->prepare('SELECT * FROM notifications ORDER BY id DESC LIMIT ?');
    $stmt->execute([$limit]);
  }
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function count_unread_notifications(){
  $db = notif_db();
  return (int)$db->query('SELECT COUNT(*) FROM notifications WHERE is_read = 0')->fetchColumn();
}

function mark_notifications_read($ids = []){
  $db = notif_db();
  if(empty($ids)){
    $db->exec('UPDATE notifications SET is_read = 1 WHERE is_read = 0');
  } else {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id IN ($placeholders)");
    $stmt->execute(array_map('intval', $ids));
  }
}

function save_push_subscription($endpoint, $p256dh, $auth){
  $db = notif_db();
  $stmt = $db->prepare('INSERT OR REPLACE INTO push_subscriptions (endpoint, p256dh, auth, created_at) VALUES (?, ?, ?, ?)');
  $stmt->execute([$endpoint, $p256dh, $auth, gmdate('c')]);
}

function get_push_subscriptions(){
  $db = notif_db();
  return $db->query('SELECT * FROM push_subscriptions')->fetchAll(PDO::FETCH_ASSOC);
}

// ========== Web Push (VAPID) ==========
function get_vapid_keys(){
  $path = DATA_DIR . '/vapid.json';
  if(file_exists($path)){
    $k = json_decode(file_get_contents($path), true);
    if(!empty($k['public_key']) && !empty($k['private_pem'])) return $k;
  }
  // Ensure OpenSSL config is found
  $cnf = '';
  foreach(['C:/php/extras/ssl/openssl.cnf', '/etc/ssl/openssl.cnf', '/usr/local/ssl/openssl.cnf'] as $f){
    if(file_exists($f)){ $cnf = $f; break; }
  }
  $opts = ['curve_name'=>'prime256v1','private_key_type'=>OPENSSL_KEYTYPE_EC];
  if($cnf) $opts['config'] = $cnf;
  $key = openssl_pkey_new($opts);
  if(!$key){ file_put_contents(DATA_DIR.'/push.log', gmdate('c')." VAPID keygen failed: ".openssl_error_string()."\n", FILE_APPEND); return ['public_key'=>'','private_pem'=>'']; }
  $det = openssl_pkey_get_details($key);
  openssl_pkey_export($key, $pem, null, $cnf ? ['config'=>$cnf] : []);
  $pub = "\x04" . $det['ec']['x'] . $det['ec']['y'];
  $data = ['public_key'=>base64url_encode($pub), 'private_pem'=>$pem, 'created'=>gmdate('c')];
  file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
  return $data;
}

function der_sig_to_raw($der){
  if(strlen($der) < 8) return str_repeat("\x00", 64);
  $pos = 2;
  // skip 0x30 + length byte(s)
  if(ord($der[1]) > 127) $pos += (ord($der[1]) & 0x7f);
  if($pos >= strlen($der) || ord($der[$pos]) !== 2) return str_repeat("\x00", 64);
  $pos++;
  $rLen = ord($der[$pos++]);
  $r = substr($der, $pos, $rLen); $pos += $rLen;
  if($pos >= strlen($der)) return str_repeat("\x00", 64);
  $pos++; // 0x02
  $sLen = ord($der[$pos++]);
  $s = substr($der, $pos, $sLen);
  return str_pad(ltrim($r,"\x00"),32,"\x00",STR_PAD_LEFT) . str_pad(ltrim($s,"\x00"),32,"\x00",STR_PAD_LEFT);
}

function vapid_auth_header($endpoint, $vapid, $subject){
  $aud = parse_url($endpoint, PHP_URL_SCHEME).'://'.parse_url($endpoint, PHP_URL_HOST);
  $h = base64url_encode(json_encode(['typ'=>'JWT','alg'=>'ES256']));
  $p = base64url_encode(json_encode(['aud'=>$aud,'exp'=>time()+43200,'sub'=>$subject]));
  $input = "$h.$p";
  $pk = openssl_pkey_get_private($vapid['private_pem']);
  if(!$pk) return '';
  openssl_sign($input, $sig, $pk, OPENSSL_ALGO_SHA256);
  return "vapid t=$input.".base64url_encode(der_sig_to_raw($sig)).", k=".$vapid['public_key'];
}

function send_push_to_sub($sub){
  try {
    $vapid = get_vapid_keys();
    if(empty($vapid['private_pem'])) return false;
    $S = get_settings();
    $subj = 'mailto:'.($S['contact_email'] ?? 'admin@example.com');
    $ep = $sub['endpoint'];
    $auth = vapid_auth_header($ep, $vapid, $subj);
    $ch = curl_init($ep);
    curl_setopt_array($ch, [
      CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>'',
      CURLOPT_HTTPHEADER=>['Authorization: '.$auth,'TTL: 86400','Urgency: high','Content-Length: 0','Topic: proomnes-alert'],
      CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code===404||$code===410){
      $db=notif_db(); $db->prepare('DELETE FROM push_subscriptions WHERE endpoint=?')->execute([$ep]);
    }
    return $code>=200 && $code<300;
  } catch(Exception $e){
    file_put_contents(DATA_DIR.'/push.log', gmdate('c')." push err: ".$e->getMessage()."\n", FILE_APPEND);
    return false;
  }
}

function broadcast_push(){
  $subs = get_push_subscriptions();
  foreach($subs as $s){ send_push_to_sub($s); }
}

// ========== Client Push Notifications ==========
function save_client_push_subscription($endpoint, $p256dh, $auth){
  $db = notif_db();
  $stmt = $db->prepare('INSERT OR REPLACE INTO client_push_subscriptions (endpoint, p256dh, auth, created_at) VALUES (?, ?, ?, ?)');
  $stmt->execute([$endpoint, $p256dh, $auth, gmdate('c')]);
}

function remove_client_push_subscription($endpoint){
  $db = notif_db();
  $stmt = $db->prepare('DELETE FROM client_push_subscriptions WHERE endpoint = ?');
  $stmt->execute([$endpoint]);
}

function get_client_push_subscriptions(){
  $db = notif_db();
  return $db->query('SELECT * FROM client_push_subscriptions')->fetchAll(PDO::FETCH_ASSOC);
}

function create_client_broadcast($title, $body = '', $link = ''){
  $db = notif_db();
  $stmt = $db->prepare('INSERT INTO client_broadcasts (title, body, link, created_at) VALUES (?, ?, ?, ?)');
  $stmt->execute([$title, $body, $link, gmdate('c')]);
  $id = $db->lastInsertId();
  // Push to all client subscribers
  try { broadcast_client_push(); } catch(Exception $e){
    file_put_contents(DATA_DIR.'/push.log', gmdate('c')." client broadcast err: ".$e->getMessage()."\n", FILE_APPEND);
  }
  return $id;
}

function get_latest_client_broadcast($within_seconds = 300){
  $db = notif_db();
  $stmt = $db->prepare('SELECT * FROM client_broadcasts ORDER BY id DESC LIMIT 1');
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if(!$row) return null;
  $age = time() - strtotime($row['created_at']);
  if($age > $within_seconds) return null;
  return $row;
}

function get_client_broadcasts($limit = 20){
  $db = notif_db();
  $stmt = $db->prepare('SELECT * FROM client_broadcasts ORDER BY id DESC LIMIT ?');
  $stmt->execute([$limit]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function count_client_subscribers(){
  $db = notif_db();
  return (int)$db->query('SELECT COUNT(*) FROM client_push_subscriptions')->fetchColumn();
}

function send_push_to_client_sub($sub){
  try {
    $vapid = get_vapid_keys();
    if(empty($vapid['private_pem'])) return false;
    $S = get_settings();
    $subj = 'mailto:'.($S['contact_email'] ?? 'admin@example.com');
    $ep = $sub['endpoint'];
    $auth = vapid_auth_header($ep, $vapid, $subj);
    $ch = curl_init($ep);
    curl_setopt_array($ch, [
      CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>'',
      CURLOPT_HTTPHEADER=>['Authorization: '.$auth,'TTL: 86400','Urgency: high','Content-Length: 0','Topic: proomnes-client'],
      CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code===404||$code===410){
      $db=notif_db(); $db->prepare('DELETE FROM client_push_subscriptions WHERE endpoint=?')->execute([$ep]);
    }
    return $code>=200 && $code<300;
  } catch(Exception $e){
    file_put_contents(DATA_DIR.'/push.log', gmdate('c')." client push err: ".$e->getMessage()."\n", FILE_APPEND);
    return false;
  }
}

function broadcast_client_push(){
  $subs = get_client_push_subscriptions();
  foreach($subs as $s){ send_push_to_client_sub($s); }
}

// ========== Admin Helper Functions ==========
if (!function_exists('admin_info')) {
  function admin_info() {
    $path = DATA_DIR . '/admin.json';
    if (!file_exists($path)) {
      // إنشاء ملف مبدئي إن لم يكن موجوداً
      $default = [
        'email' => 'owner@example.com',
        'password_hash' => '',
        'created' => date('Y-m-d H:i:s')
      ];
      file_put_contents($path, json_encode($default, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
      return $default;
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return $data ?: ['email' => 'owner@example.com', 'password_hash' => ''];
  }
}

if (!function_exists('admin_save')) {
  function admin_save($data) {
    $path = DATA_DIR . '/admin.json';
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    return true;
  }
}
?>
