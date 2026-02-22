<?php include __DIR__.'/_header.php'; csrf_verify(); $S = get_settings(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $S['integrations']['webhook_url'] = $_POST['webhook_url'] ?? $S['integrations']['webhook_url'];
  $S['integrations']['hubspot_portal_id'] = $_POST['hubspot_portal_id'] ?? $S['integrations']['hubspot_portal_id'];
  $S['integrations']['hubspot_form_guid'] = $_POST['hubspot_form_guid'] ?? $S['integrations']['hubspot_form_guid'];
  $S['integrations']['hubspot_token'] = $_POST['hubspot_token'] ?? $S['integrations']['hubspot_token'];
  $S['integrations']['zoho_access_token'] = $_POST['zoho_access_token'] ?? $S['integrations']['zoho_access_token'];
  save_json('settings.json', $S);
  $msg='تم الحفظ';
}
?>
<h1>التكاملات (CRM/Webhooks)</h1>
<?php if($msg): ?><div class="card"><?php echo esc($msg); ?></div><?php endif; ?>
<form method="post" class="form">
  <?php csrf_field(); ?>
  <h3>Webhook</h3>
  <label>Endpoint URL</label><input class="input" name="webhook_url" value="<?php echo esc($S['integrations']['webhook_url']); ?>">
  <h3>HubSpot</h3>
  <label>Portal ID</label><input class="input" name="hubspot_portal_id" value="<?php echo esc($S['integrations']['hubspot_portal_id']); ?>">
  <label>Form GUID</label><input class="input" name="hubspot_form_guid" value="<?php echo esc($S['integrations']['hubspot_form_guid']); ?>">
  <label>Private App Token</label><input class="input" name="hubspot_token" value="<?php echo esc($S['integrations']['hubspot_token']); ?>">
  <h3>Zoho CRM</h3>
  <label>Access Token</label><input class="input" name="zoho_access_token" value="<?php echo esc($S['integrations']['zoho_access_token']); ?>">
  <button class="btn btn-primary" type="submit">حفظ</button>
</form>
<?php include __DIR__.'/_footer.php'; ?>