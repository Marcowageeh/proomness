<?php
// admin/ai_settings.php – Manage OpenAI integration settings
require_once __DIR__.'/_header.php';
csrf_verify();
require_admin();

// Load current AI settings
$AI = get_ai_settings();
$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  // Save settings. No validation here; ensure to enter correct values later.
  $AI['api_key']      = trim($_POST['api_key'] ?? '');
  $AI['model']        = trim($_POST['model'] ?? '');
  $AI['temperature']  = trim($_POST['temperature'] ?? '');
  $AI['max_tokens']   = trim($_POST['max_tokens'] ?? '');
  $AI['system_prompt'] = trim($_POST['system_prompt'] ?? '');
  save_ai_settings($AI);
  $msg = 'تم حفظ إعدادات OpenAI بنجاح';
}
?>
<h1>إعدادات OpenAI</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <label>API Key</label>
  <input class="input" type="text" name="api_key" value="<?php echo esc($AI['api_key'] ?? ''); ?>">
  <label>Model (e.g., gpt-3.5-turbo, gpt-4)</label>
  <input class="input" type="text" name="model" value="<?php echo esc($AI['model'] ?? 'gpt-3.5-turbo'); ?>">
  <label>Temperature (0-1)</label>
  <input class="input" type="number" step="0.01" min="0" max="1" name="temperature" value="<?php echo esc($AI['temperature'] ?? '0.7'); ?>">
  <label>Max Tokens</label>
  <input class="input" type="number" name="max_tokens" value="<?php echo esc($AI['max_tokens'] ?? '500'); ?>">
  <label>System Prompt</label>
  <textarea class="input" name="system_prompt" rows="4"><?php echo esc($AI['system_prompt'] ?? ''); ?></textarea>
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>