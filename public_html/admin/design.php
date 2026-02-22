<?php
// Admin design management interface
// This page allows administrators to update the site's theme colours and hero image.
// It reads values from data/design.json via get_design() and saves updates back using save_json().
require_once __DIR__.'/_header.php';
csrf_verify();
require_admin();

// Load current design settings
$D = get_design();
$available_templates = [
  'theme-default' => 'افتراضي',
  'theme-light'   => 'فاتح',
  'theme-sunset'  => 'غروب',
  'theme-forest'  => 'غابة',
  'theme-ocean'   => 'محيط',
  'theme-modern'  => 'حديث'
  ,
  'theme-binance' => 'بينانس'
];
// Define available font families and their Google Fonts import URLs. Add more professional fonts for flexibility.
$available_fonts = [
  'Cairo'      => 'Cairo',
  'Tajawal'    => 'Tajawal',
  'Roboto'     => 'Roboto',
  'Montserrat' => 'Montserrat',
  'Poppins'    => 'Poppins',
  'Inter'      => 'Inter',
  'Lato'       => 'Lato',
  'Open Sans'  => 'Open Sans',
  'Playfair Display' => 'Playfair Display',
  'Merriweather' => 'Merriweather'
];
$font_urls = [
  'Cairo'      => 'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap',
  'Tajawal'    => 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700&display=swap',
  'Roboto'     => 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
  'Montserrat' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap',
  'Poppins'    => 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
  'Inter'      => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
  'Lato'       => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap',
  'Open Sans'  => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap',
  'Playfair Display' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap',
  'Merriweather' => 'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap'
];
$msg = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Update colour values from form inputs. Use fallback to existing values if not set.
  $D['primary_color']  = trim($_POST['primary_color'] ?? ($D['primary_color']  ?? '#5d7bff'));
  $D['primary_color2'] = trim($_POST['primary_color2'] ?? ($D['primary_color2'] ?? '#7aa0ff'));
  $D['accent_color']   = trim($_POST['accent_color'] ?? ($D['accent_color']   ?? '#22d1ee'));
  // Template selection
  $selected_template = $_POST['template'] ?? ($D['template'] ?? 'theme-default');
  if(isset($available_templates[$selected_template])){
    $D['template'] = $selected_template;
  }
  // Font selection and mapping to URL
  $selected_font = $_POST['font'] ?? ($D['font'] ?? 'Cairo');
  if(isset($available_fonts[$selected_font]) && isset($font_urls[$selected_font])){
    $D['font'] = $selected_font;
    $D['font_url'] = $font_urls[$selected_font];
  }
  // Support script
  $D['support_script'] = trim($_POST['support_script'] ?? ($D['support_script'] ?? ''));
  // Typography and design settings
  $D['font_size_base']    = trim($_POST['font_size_base']    ?? ($D['font_size_base']    ?? ''));
  $D['font_size_heading'] = trim($_POST['font_size_heading'] ?? ($D['font_size_heading'] ?? ''));
  $D['image_opacity']     = trim($_POST['image_opacity']     ?? ($D['image_opacity']     ?? ''));
  $D['card_radius']       = trim($_POST['card_radius']       ?? ($D['card_radius']       ?? ''));
  $D['card_shadow']       = trim($_POST['card_shadow']       ?? ($D['card_shadow']       ?? ''));
  $D['bg_opacity']        = trim($_POST['bg_opacity']        ?? ($D['bg_opacity']        ?? ''));
  // Text colours for dark and light themes
  // Allow setting custom text colour for dark mode and light mode. If not provided, keep existing value.
  $D['dark_text_color']   = trim($_POST['dark_text_color']   ?? ($D['dark_text_color']   ?? ''));
  $D['light_text_color']  = trim($_POST['light_text_color']  ?? ($D['light_text_color']  ?? ''));

  // Background colours for cards/sections in dark and light themes
  // These values allow administrators to customise the background colour used for
  // various boxes, cards and section containers across the site. If left empty
  // the default theme values defined in the CSS will be used.
  $D['dark_surface_color']  = trim($_POST['dark_surface_color']  ?? ($D['dark_surface_color']  ?? ''));
  $D['light_surface_color'] = trim($_POST['light_surface_color'] ?? ($D['light_surface_color'] ?? ''));
  // Section visibility toggles (checkboxes) – default to '1' (visible) if not set
  $D['show_services']     = isset($_POST['show_services']) ? '1' : '0';
  $D['show_pricing']      = isset($_POST['show_pricing'])  ? '1' : '0';
  $D['show_process']      = isset($_POST['show_process'])  ? '1' : '0';
  $D['show_why']          = isset($_POST['show_why'])      ? '1' : '0';
  $D['show_final_cta']    = isset($_POST['show_final_cta'])? '1' : '0';
  $D['show_radio']        = isset($_POST['show_radio'])     ? '1' : '0';
  // New visibility toggles for additional sections and buttons
  $D['show_about']        = isset($_POST['show_about'])    ? '1' : '0';
  $D['show_contact']      = isset($_POST['show_contact'])  ? '1' : '0';
  $D['show_brief']        = isset($_POST['show_brief'])    ? '1' : '0';
  $D['show_blog']         = isset($_POST['show_blog'])     ? '1' : '0';
  $D['show_payment']      = isset($_POST['show_payment'])  ? '1' : '0';
  $D['show_faq']          = isset($_POST['show_faq'])      ? '1' : '0';
  $D['show_quote']        = isset($_POST['show_quote'])    ? '1' : '0';
  $D['show_start']        = isset($_POST['show_start'])    ? '1' : '0';
  // Handle hero image upload securely.
  // Only allow common image types and limit file size to 2 MB.
  if(!empty($_FILES['hero_image']['name']) && is_uploaded_file($_FILES['hero_image']['tmp_name'])){
    $allowed_types = ['image/png','image/jpeg','image/svg+xml'];
    $max_size = 2 * 1024 * 1024;
    $mime = @mime_content_type($_FILES['hero_image']['tmp_name']);
    $size = (int)($_FILES['hero_image']['size'] ?? 0);
    if(in_array($mime, $allowed_types, true) && $size > 0 && $size <= $max_size){
      $fn = basename($_FILES['hero_image']['name']);
      // Sanitize filename to avoid special characters and spaces
      $safe = preg_replace('/[^a-zA-Z0-9_\.\-]/','_', $fn);
      // Make sure the upload directory exists; create it if necessary
      if(!is_dir(UPLOAD_DIR)){
        mkdir(UPLOAD_DIR, 0775, true);
      }
      $target = UPLOAD_DIR . '/' . time() . '_' . $safe;
      if(move_uploaded_file($_FILES['hero_image']['tmp_name'], $target)){
        $rel = 'assets/uploads/' . basename($target);
        $D['hero_image'] = $rel;
      }
    }
  }
  save_json('design.json', $D);
  $msg = 'تم حفظ التصميم بنجاح';
}
?>
<h1>إدارة التصميم</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" enctype="multipart/form-data">
  <?php csrf_field(); ?>
  <label>اللون الأساسي</label>
  <input class="input" type="color" name="primary_color" value="<?php echo esc($D['primary_color'] ?? '#5d7bff'); ?>">
  <label>اللون الثانوي</label>
  <input class="input" type="color" name="primary_color2" value="<?php echo esc($D['primary_color2'] ?? '#7aa0ff'); ?>">
  <label>لون اللمسة</label>
  <input class="input" type="color" name="accent_color" value="<?php echo esc($D['accent_color'] ?? '#22d1ee'); ?>">
  <label>صورة الخلفية (Hero)</label>
  <input class="input" type="file" name="hero_image">
  <?php if(!empty($D['hero_image'])): ?>
    <p>الصورة الحالية:</p>
    <img src="/<?php echo esc($D['hero_image']); ?>" alt="Hero Image" style="max-width:200px;border-radius:6px;">
  <?php endif; ?>

  <!-- Typography and design controls -->
  <label>حجم الخط الأساسي (مثل 16px أو 1rem)</label>
  <input class="input" type="text" name="font_size_base" value="<?php echo esc($D['font_size_base'] ?? ''); ?>">
  <label>حجم العناوين الرئيسية (مثل 32px)</label>
  <input class="input" type="text" name="font_size_heading" value="<?php echo esc($D['font_size_heading'] ?? ''); ?>">
  <label>شفافية الصور (من 0 إلى 1)</label>
  <input class="input" type="number" step="0.05" min="0" max="1" name="image_opacity" value="<?php echo esc($D['image_opacity'] ?? '1'); ?>">
  <label>شفافية الخلفيات (من 0 إلى 1)</label>
  <input class="input" type="number" step="0.05" min="0" max="1" name="bg_opacity" value="<?php echo esc($D['bg_opacity'] ?? '1'); ?>">
  <label>انحناء البطاقات (Card Radius)</label>
  <input class="input" type="text" name="card_radius" value="<?php echo esc($D['card_radius'] ?? '14px'); ?>">
  <label>ظل البطاقات (Card Shadow)</label>
  <input class="input" type="text" name="card_shadow" value="<?php echo esc($D['card_shadow'] ?? '0 4px 20px rgba(0,0,0,0.25)'); ?>">
  <small class="note">يمكنك تعديل شكل ظل البطاقات باستخدام قيمة CSS box-shadow.</small>

  <!-- Text colour controls -->
  <label>لون النص في الوضع الليلي</label>
  <input class="input" type="color" name="dark_text_color" value="<?php echo esc($D['dark_text_color'] ?? '#f5f5f5'); ?>">
  <label>لون النص في الوضع النهاري</label>
  <input class="input" type="color" name="light_text_color" value="<?php echo esc($D['light_text_color'] ?? '#222222'); ?>">

  <!-- Background colour controls for cards and sections -->
  <label>لون خلفية المربعات والأقسام في الوضع الليلي</label>
  <input class="input" type="color" name="dark_surface_color" value="<?php echo esc($D['dark_surface_color'] ?? '#0f1a30'); ?>">
  <label>لون خلفية المربعات والأقسام في الوضع النهاري</label>
  <input class="input" type="color" name="light_surface_color" value="<?php echo esc($D['light_surface_color'] ?? '#f8f9fa'); ?>">

  <!-- Section visibility toggles -->
  <h2>عرض/إخفاء الأقسام</h2>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;">
    <label><input type="checkbox" name="show_services" value="1" <?php echo (($D['show_services'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار قسم الخدمات</label>
    <label><input type="checkbox" name="show_pricing" value="1" <?php echo (($D['show_pricing'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار قسم الأسعار</label>
    <label><input type="checkbox" name="show_process" value="1" <?php echo (($D['show_process'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار قسم كيف نعمل</label>
    <label><input type="checkbox" name="show_why" value="1" <?php echo (($D['show_why'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار قسم لماذا تختارنا</label>
    <label><input type="checkbox" name="show_final_cta" value="1" <?php echo (($D['show_final_cta'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار قسم الدعوة النهائية</label>
    <label><input type="checkbox" name="show_radio" value="1" <?php echo (($D['show_radio'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار رابط الراديو</label>
    <label><input type="checkbox" name="show_support" value="1" <?php echo (($D['show_support'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار رابط الدعم المباشر</label>
    <label><input type="checkbox" name="show_about" value="1" <?php echo (($D['show_about'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة من نحن</label>
    <label><input type="checkbox" name="show_contact" value="1" <?php echo (($D['show_contact'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة اتصل بنا</label>
    <label><input type="checkbox" name="show_brief" value="1" <?php echo (($D['show_brief'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة طلب مشروع</label>
    <label><input type="checkbox" name="show_blog" value="1" <?php echo (($D['show_blog'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة المدونة</label>
    <label><input type="checkbox" name="show_payment" value="1" <?php echo (($D['show_payment'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة الدفع</label>
    <label><input type="checkbox" name="show_faq" value="1" <?php echo (($D['show_faq'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار صفحة الأسئلة الشائعة</label>
    <label><input type="checkbox" name="show_quote" value="1" <?php echo (($D['show_quote'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار زر طلب عرض</label>
    <label><input type="checkbox" name="show_start" value="1" <?php echo (($D['show_start'] ?? '1') === '1') ? 'checked' : ''; ?>> إظهار زر البدء الآن</label>
  </div>

  <label>القالب</label>
  <select class="input" name="template">
    <?php foreach($available_templates as $tpl => $label): ?>
      <option value="<?php echo esc($tpl); ?>" <?php echo (($D['template'] ?? 'theme-default') === $tpl ? 'selected' : ''); ?>><?php echo esc($label); ?></option>
    <?php endforeach; ?>
  </select>

  <label>الخط</label>
  <select class="input" name="font">
    <?php foreach($available_fonts as $font => $label): ?>
      <option value="<?php echo esc($font); ?>" <?php echo (($D['font'] ?? 'Cairo') === $font ? 'selected' : ''); ?>><?php echo esc($label); ?></option>
    <?php endforeach; ?>
  </select>

  <label>كود الدعم المباشر (HTML/JS)</label>
  <textarea class="input" name="support_script" rows="4"><?php echo esc($D['support_script'] ?? ''); ?></textarea>
  <small class="note">يمكنك وضع هنا الكود الخاص بخدمة الدردشة مثل Tawk.to أو Crisp، وسيتم تضمينه في صفحة الدعم.</small>
  <button class="btn btn-primary" type="submit">حفظ التصميم</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>