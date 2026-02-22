<?php
// Public-facing page to render the news radio player.
require_once __DIR__.'/inc/functions.php';
$S   = get_settings();
$lang = get_lang();
// Load radio configuration
$R = load_json('radio.json');
// Provide sensible fallbacks for missing arrays
if(!isset($R['title']) || !is_array($R['title'])){ $R['title'] = ['ar'=>'','en'=>'','fr'=>'']; }
if(!isset($R['description']) || !is_array($R['description'])){ $R['description'] = ['ar'=>'','en'=>'','fr'=>'']; }
if(!isset($R['audio']) || !is_array($R['audio'])){ $R['audio'] = ['type'=>'','file'=>'','url'=>'']; }
$page_key = 'radio';
?>
<?php include __DIR__.'/templates/header.php'; ?>

<section class="section">
  <div class="container">
    <h1><?php echo esc(t($R['title'] ?: ($lang==='ar' ? 'راديو الأخبار' : 'News Radio'))); ?></h1>
    <?php if(!empty($R['description'])): ?>
      <p><?php echo nl2br(esc(t($R['description']))); ?></p>
    <?php endif; ?>

    <?php if(($R['audio']['type'] ?? '') === 'file' && !empty($R['audio']['file'])): ?>
      <!-- Local audio file -->
      <audio controls preload="none" style="width:100%;max-width:600px;">
        <source src="/<?php echo esc($R['audio']['file']); ?>">
        Your browser does not support the audio element.
      </audio>
    <?php elseif(($R['audio']['type'] ?? '') === 'stream' && !empty($R['audio']['url'])): ?>
      <!-- Streaming audio URL -->
      <audio controls preload="none" style="width:100%;max-width:600px;">
        <source src="<?php echo esc($R['audio']['url']); ?>">
        Your browser does not support the audio element.
      </audio>
    <?php else: ?>
      <p><?php echo $lang==='ar' ? 'لم يتم إضافة بث أو ملف صوتي بعد.' : 'No audio stream or file configured yet.'; ?></p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__.'/templates/footer.php'; ?>