<?php $page_key='services'; require __DIR__.'/templates/header.php'; $SVC = load_services(); 
$slug = $_GET['slug'] ?? 'application-management'; $sv = $SVC[$slug] ?? reset($SVC); ?>
<section class="section">
  <div class="container">
    <div class="breadcrumbs"><a href="/index.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الرئيسية':'Home'; ?></a> / 
      <a href="/services.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الخدمات':'Services'; ?></a> / 
      <span><?php echo esc(t($sv['title'])); ?></span></div>
    <h1 class="mt-0"><?php echo esc(t($sv['title'])); ?></h1>
    <p class="lead"><?php echo esc(t($sv['desc'])); ?></p>
    <div class="grid-2 mt-2">
      <div class="card">
        <h3 class="mt-0"><?php echo $lang==='ar'?'بنود العمل':'Deliverables'; ?></h3>
        <ul class="features">
          <?php foreach(($sv['deliver']??[]) as $i): ?><li><?php echo esc(t($i)); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="card">
        <h3 class="mt-0"><?php echo $lang==='ar'?'نسق العمل':'Workflow'; ?></h3>
        <ul class="features">
          <?php foreach(($sv['flow']??[]) as $i): ?><li><?php echo esc(t($i)); ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
    <div class="grid-2 mt-2">
      <div class="card">
        <h3 class="mt-0"><?php echo $lang==='ar'?'النتائج المتوقعة':'Expected outcomes'; ?></h3>
        <ul class="features">
          <?php foreach(($sv['results']??[]) as $i): ?><li><?php echo esc(t($i)); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="card">
        <h3 class="mt-0"><?php echo $lang==='ar'?'المدة والسعر المبدئي':'Duration & starting price'; ?></h3>
        <table class="table">
          <tr><th><?php echo $lang==='ar'?'المدة':'Duration'; ?></th><td><?php echo esc(t($sv['duration'])); ?></td></tr>
          <tr><th><?php echo $lang==='ar'?'السعر المبدئي':'Starting price'; ?></th><td><?php echo esc(t($sv['price'])); ?></td></tr>
        </table>
        <p class="mt-1"><a class="btn btn-primary" href="/brief.php?service=<?php echo esc($slug); ?>&lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'اطلب عرضًا مخصّصًا':'Request a custom quote'; ?></a></p>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__.'/templates/footer.php'; ?>
