<?php $page_key='brief'; require __DIR__.'/templates/header.php'; csrf_verify(); $S = get_settings();
$ok = false; $lead_id=null; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??'');
  $company=trim($_POST['company']??''); $website=trim($_POST['website']??''); $service=trim($_POST['service']??'');
  $budget=trim($_POST['budget']??''); $deadline=trim($_POST['deadline']??''); $features = isset($_POST['features']) ? implode(', ', $_POST['features']) : '';
  $message = trim($_POST['message']??'');
  if(!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)){
    $err = $lang==='ar'?'الاسم والبريد الإلكتروني مطلوبان':'Name and a valid email are required';
  } else {
    // file upload with validation
    $file_path = '';
    $allowed_ext = ['pdf','doc','docx','xls','xlsx','ppt','pptx','txt','zip','rar','jpg','jpeg','png','gif','webp'];
    $max_upload = 5 * 1024 * 1024; // 5MB
    if(!empty($_FILES['brief_file']['name']) && $_FILES['brief_file']['error'] === UPLOAD_ERR_OK){
      $fn = basename($_FILES['brief_file']['name']);
      $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
      $size = (int)$_FILES['brief_file']['size'];
      if(!in_array($ext, $allowed_ext)){
        $err = $lang==='ar'?'نوع الملف غير مسموح':'File type not allowed';
      } elseif($size > $max_upload){
        $err = $lang==='ar'?'حجم الملف يتجاوز 5MB':'File exceeds 5MB limit';
      } else {
        $safe = preg_replace('/[^a-zA-Z0-9_\.-]/','_', $fn);
        if(!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
        $target = UPLOAD_DIR . '/' . time() . '_' . $safe;
        if(move_uploaded_file($_FILES['brief_file']['tmp_name'], $target)){
          $file_path = 'assets/uploads/' . basename($target);
        }
      }
    }
    $lead_id = save_lead(compact('name','email','phone','company','website','service','budget','deadline','features','message','file_path'));
    $ok = true;
    // Create admin notification
    create_notification(
      'lead',
      ($lang==='ar'?'طلب مشروع جديد #':'New project brief #') . $lead_id,
      $name . ' — ' . $service . ' — ' . $budget,
      '/admin/leads.php'
    );
    integration_push(['name'=>$name,'email'=>$email,'phone'=>$phone,'company'=>$company,'website'=>$website,'service'=>$service,'budget'=>$budget,'deadline'=>$deadline,'features'=>$features,'message'=>$message]);
    // send email
    $subject = ($lang==='ar'?'طلب مشروع جديد #':'New project brief #') . $lead_id;
    $body = "ID: $lead_id\nName: $name\nEmail: $email\nPhone: $phone\nCompany: $company\nWebsite: $website\nService: $service\nBudget: $budget\nDeadline: $deadline\nFeatures: $features\nMessage:\n$message\nFile: $file_path\n";
    send_email($S['contact_email'], $subject, $body);
  }
}
?>
<section class="section">
  <div class="container">
    <div class="breadcrumbs"><a href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الرئيسية':'Home'; ?></a> / <span><?php echo $lang==='ar'?'طلب مشروع':'Project Brief'; ?></span></div>
    <h1 class="mt-0"><?php echo $lang==='ar'?'طلب مشروع جديد':'New Project Brief'; ?></h1>
    <?php if($ok): ?>
      <div class="card">
        <h3><?php echo $lang==='ar'?'تم استلام طلبك':'Your brief was received'; ?> #<?php echo (int)$lead_id; ?></h3>
        <p><?php echo $lang==='ar'?'سنعاود الاتصال خلال يوم عمل.':'We’ll get back within one business day.'; ?></p>
        <p><a class="btn btn-primary" href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'عودة للرئيسية':'Back to home'; ?></a></p>
      </div>
    <?php else: ?>
      <?php if($err): ?><div class="card" style="border-color:#ef476f"><?php echo esc($err); ?></div><?php endif; ?>
      <div class="grid-2 mt-2">
        <div class="card">
          <form class="form" method="post" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <div><label><?php echo $lang==='ar'?'الاسم الكامل':'Full name'; ?></label><input class="input" name="name" required></div>
            <div><label><?php echo $lang==='ar'?'البريد الإلكتروني':'Email'; ?></label><input class="input" type="email" name="email" required></div>
            <div><label><?php echo $lang==='ar'?'الهاتف':'Phone'; ?></label><input class="input" name="phone"></div>
            <div><label><?php echo $lang==='ar'?'الشركة/المشروع':'Company/Project'; ?></label><input class="input" name="company"></div>
            <div><label>Website</label><input class="input" name="website" placeholder="https://"></div>
            <div><label><?php echo $lang==='ar'?'نوع الخدمة':'Service'; ?></label>
              <select class="input" name="service">
                <?php foreach((load_home()['services'] ?? []) as $svc): ?>
                  <option value="<?php echo esc($svc['slug']); ?>"><?php echo esc(t($svc['title'])); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div><label><?php echo $lang==='ar'?'الميزانية التقريبية':'Estimated budget'; ?></label>
              <select class="input" name="budget">
                <option>< 300$</option><option>300$–1,000$</option><option>1,000$–5,000$</option><option>> 5,000$</option>
              </select>
            </div>
            <div><label><?php echo $lang==='ar'?'المدة المتوقعة / الموعد النهائي':'Desired deadline'; ?></label><input class="input" name="deadline" placeholder="YYYY-MM-DD"></div>
            <div>
              <label><?php echo $lang==='ar'?'ميزات مطلوبة':'Required features'; ?></label>
              <div>
                <label><input type="checkbox" name="features[]" value="Dashboard"> Dashboard</label>
                <label><input type="checkbox" name="features[]" value="CMS"> CMS</label>
                <label><input type="checkbox" name="features[]" value="E-commerce"> E‑commerce</label>
                <label><input type="checkbox" name="features[]" value="API"> API</label>
                <label><input type="checkbox" name="features[]" value="Multilingual"> Multilingual</label>
              </div>
            </div>
            <div><label><?php echo $lang==='ar'?'وصف مختصر للاحتياج':'Short brief'; ?></label><textarea class="input" name="message" rows="6"></textarea></div>
            <div><label><?php echo $lang==='ar'?'ملف مرفق (اختياري)':'Attachment (optional)'; ?></label><input class="input" type="file" name="brief_file"></div>
            <div><button class="btn btn-primary" type="submit"><?php echo $lang==='ar'?'إرسال الطلب':'Submit brief'; ?></button></div>
          </form>
        </div>
        <div class="card">
          <h3><?php echo $lang==='ar'?'لماذا نطلب هذه المعلومات؟':'Why these details?'; ?></h3>
          <p><?php echo $lang==='ar'?'لتقدير الجهد والمدة وتحديد الفريق المناسب.':'They help us estimate scope, timeline, and the right team.'; ?></p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
