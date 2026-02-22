<?php
// Admin interface to manage the news radio feature.
// Allows setting localized titles, descriptions, and uploading an audio file or specifying a streaming URL.
require_once __DIR__.'/_header.php';
csrf_verify();
require_admin();

// Load current radio configuration
$R = load_json('radio.json');
$msg = '';

// Ensure nested arrays exist for localisation
if(!isset($R['title']) || !is_array($R['title'])){ $R['title'] = ['ar'=>'','en'=>'','fr'=>'']; }
if(!isset($R['description']) || !is_array($R['description'])){ $R['description'] = ['ar'=>'','en'=>'','fr'=>'']; }
if(!isset($R['audio']) || !is_array($R['audio'])){ $R['audio'] = ['type'=>'','file'=>'','url'=>'']; }

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Update titles and descriptions from POST data
  $R['title']['ar'] = trim($_POST['title_ar'] ?? $R['title']['ar']);
  $R['title']['en'] = trim($_POST['title_en'] ?? $R['title']['en']);
  $R['title']['fr'] = trim($_POST['title_fr'] ?? $R['title']['fr']);
  $R['description']['ar'] = trim($_POST['desc_ar'] ?? $R['description']['ar']);
  $R['description']['en'] = trim($_POST['desc_en'] ?? $R['description']['en']);
  $R['description']['fr'] = trim($_POST['desc_fr'] ?? $R['description']['fr']);
  // Determine audio type: file or stream
  $type = $_POST['audio_type'] ?? $R['audio']['type'];
  // Sanitise type
  $type = ($type === 'file' || $type === 'stream') ? $type : '';
  $R['audio']['type'] = $type;
  // If file upload selected
  if($type === 'file' && !empty($_FILES['audio_file']['name']) && is_uploaded_file($_FILES['audio_file']['tmp_name'])){
    // Restrict to safe MIME types and size <= 20 MB
    $allowed_audio = ['audio/mpeg','audio/mp3','audio/x-mpeg','audio/x-mp3','audio/x-wav','audio/wav','audio/x-aac','audio/aac','audio/ogg','audio/webm'];
    $max_size = 20 * 1024 * 1024; // 20 MB
    $mime = @mime_content_type($_FILES['audio_file']['tmp_name']);
    $size = (int)($_FILES['audio_file']['size'] ?? 0);
    if(in_array($mime, $allowed_audio, true) && $size > 0 && $size <= $max_size){
      // Sanitize filename
      $fn = basename($_FILES['audio_file']['name']);
      $safe = preg_replace('/[^a-zA-Z0-9_\.\-]/','_', $fn);
      // Ensure upload directory exists
      if(!is_dir(UPLOAD_DIR)){
        mkdir(UPLOAD_DIR, 0775, true);
      }
      $target = UPLOAD_DIR . '/' . time() . '_' . $safe;
      if(move_uploaded_file($_FILES['audio_file']['tmp_name'], $target)){
        $rel = 'assets/uploads/' . basename($target);
        $R['audio'] = ['type'=>'file', 'file'=>$rel, 'url'=>''];
      }
    }
  }
  // If stream selected
  if($type === 'stream'){
    $url = trim($_POST['audio_url'] ?? '');
    // Basic URL validation
    if($url !== '' && filter_var($url, FILTER_VALIDATE_URL)){
      $R['audio'] = ['type'=>'stream', 'file'=>'', 'url'=>$url];
    }
  }
  // Save updated configuration
  save_json('radio.json', $R);
  $msg = 'تم حفظ إعدادات الراديو بنجاح';
}
?>
<h1>راديو الأخبار</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form" enctype="multipart/form-data">
  <?php csrf_field(); ?>
  <label>العنوان بالعربية</label>
  <input class="input" name="title_ar" value="<?php echo esc($R['title']['ar'] ?? ''); ?>">
  <label>العنوان بالإنجليزية</label>
  <input class="input" name="title_en" value="<?php echo esc($R['title']['en'] ?? ''); ?>">
  <label>العنوان بالفرنسية</label>
  <input class="input" name="title_fr" value="<?php echo esc($R['title']['fr'] ?? ''); ?>">

  <label>الوصف بالعربية</label>
  <textarea class="input" name="desc_ar" rows="3"><?php echo esc($R['description']['ar'] ?? ''); ?></textarea>
  <label>الوصف بالإنجليزية</label>
  <textarea class="input" name="desc_en" rows="3"><?php echo esc($R['description']['en'] ?? ''); ?></textarea>
  <label>الوصف بالفرنسية</label>
  <textarea class="input" name="desc_fr" rows="3"><?php echo esc($R['description']['fr'] ?? ''); ?></textarea>

  <label>نوع الصوت</label>
  <select class="input" name="audio_type">
    <option value="file" <?php echo (($R['audio']['type'] ?? '') === 'file' ? 'selected' : ''); ?>>ملف صوتي</option>
    <option value="stream" <?php echo (($R['audio']['type'] ?? '') === 'stream' ? 'selected' : ''); ?>>رابط بث مباشر</option>
  </select>

  <label>ملف صوتي (MP3/WAV/Ogg بحد أقصى 20 ميجا)</label>
  <input class="input" type="file" name="audio_file">
  <?php if(!empty($R['audio']['file'])): ?>
    <p>الملف الحالي: <code><?php echo esc($R['audio']['file']); ?></code></p>
  <?php endif; ?>

  <label>رابط البث المباشر (Stream URL)</label>
  <input class="input" name="audio_url" value="<?php echo esc($R['audio']['url'] ?? ''); ?>">
  <?php if(!empty($R['audio']['url'])): ?>
    <p>الرابط الحالي: <code><?php echo esc($R['audio']['url']); ?></code></p>
  <?php endif; ?>

  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>