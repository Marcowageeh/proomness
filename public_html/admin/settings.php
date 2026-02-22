<?php include __DIR__.'/_header.php'; csrf_verify(); $S = get_settings(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $S['site_name'] = trim($_POST['site_name']??$S['site_name']);
  $S['contact_email'] = trim($_POST['contact_email']??$S['contact_email']);
  $S['phone'] = trim($_POST['phone']??$S['phone']);
  $S['domain'] = trim($_POST['domain']??$S['domain']);
  $S['default_lang'] = trim($_POST['default_lang']??$S['default_lang']);
  $langs = array_filter(array_map('trim', explode(',', $_POST['languages'] ?? 'ar,en')));
  $S['languages'] = array_values(array_unique($langs));
  $S['slogan']['ar'] = $_POST['slogan_ar'] ?? $S['slogan']['ar'];
  $S['slogan']['en'] = $_POST['slogan_en'] ?? $S['slogan']['en'];
  $S['slogan']['fr'] = $_POST['slogan_fr'] ?? ($S['slogan']['fr'] ?? '');
  // Upload logos
  // Only allow certain image types and sizes when uploading logos.
  // We restrict uploads to common image MIME types and a maximum file size of 2 MB.
  $allowed_types = ['image/png','image/jpeg','image/svg+xml'];
  $max_size = 2 * 1024 * 1024; // 2 MB
  foreach(['primary'=>'logo_primary','secondary'=>'logo_secondary','og'=>'logo_og'] as $k=>$field){
    if(!empty($_FILES[$field]['name']) && is_uploaded_file($_FILES[$field]['tmp_name'])){
      // Determine the MIME type of the uploaded file
      $mime = @mime_content_type($_FILES[$field]['tmp_name']);
      $size = (int)($_FILES[$field]['size'] ?? 0);
      if(in_array($mime, $allowed_types, true) && $size > 0 && $size <= $max_size){
        $fn = basename($_FILES[$field]['name']);
        // Sanitize the filename to prevent directory traversal and special characters
        $safe = preg_replace('/[^a-zA-Z0-9_\.-]/','_', $fn);
        // Ensure the upload directory exists; create it if necessary
        if(!is_dir(UPLOAD_DIR)){
          mkdir(UPLOAD_DIR, 0775, true);
        }
        $target = UPLOAD_DIR . '/' . time() . '_' . $safe;
        if(move_uploaded_file($_FILES[$field]['tmp_name'], $target)){
          // Save relative path for later use in templates
          $rel = 'assets/uploads/' . basename($target);
          $S['logos'][$k] = $rel;
        }
      }
    }
  }
  save_json('settings.json', $S);
  $msg='تم الحفظ';
}
?>
<h1>الإعدادات</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" enctype="multipart/form-data">
  <?php csrf_field(); ?>
  <div class="grid-2">
    <div>
      <label>اسم الموقع</label><input class="input" name="site_name" value="<?php echo esc($S['site_name']); ?>">
      <label>البريد للتواصل</label><input class="input" name="contact_email" value="<?php echo esc($S['contact_email']); ?>">
      <label>الهاتف</label><input class="input" name="phone" value="<?php echo esc($S['phone']); ?>">
      <label>النطاق (Domain)</label><input class="input" name="domain" value="<?php echo esc($S['domain']); ?>">
      <label>اللغة الافتراضية</label><input class="input" name="default_lang" value="<?php echo esc($S['default_lang']); ?>">
      <label>اللغات (مفصولة بفواصل)</label><input class="input" name="languages" value="<?php echo esc(implode(',',$S['languages'])); ?>">
      <label>الشعار (ar/en/fr)</label>
      <input class="input" name="slogan_ar" value="<?php echo esc($S['slogan']['ar']); ?>">
      <input class="input" name="slogan_en" value="<?php echo esc($S['slogan']['en']); ?>">
      <input class="input" name="slogan_fr" value="<?php echo esc($S['slogan']['fr'] ?? ''); ?>">
    </div>
    <div>
      <label>شعار أساسي</label><input class="input" type="file" name="logo_primary">
      <label>شعار ثانوي</label><input class="input" type="file" name="logo_secondary">
      <label>صورة Open Graph</label><input class="input" type="file" name="logo_og">
    </div>
  </div>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>