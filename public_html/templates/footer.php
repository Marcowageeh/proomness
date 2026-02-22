<?php $S = get_settings(); $lang = get_lang(); ?>
</main>
<footer class="footer">
  <div class="container">
    <div class="cols">
      <div>
        <div class="brand">
          <img src="/<?php echo esc($S['logos']['secondary']); ?>" alt="<?php echo esc($S['site_name']); ?>">
          <div>
            <div class="name"><?php echo esc($S['site_name']); ?></div>
            <div class="kicker"><?php echo esc(t($S['slogan'])); ?></div>
          </div>
        </div>
        <p class="mt-1"><?php echo $lang==='ar'?'نبني حلولاً رقمية احترافية تضع عملك في المقدمة.':'We build professional digital solutions that put your business ahead.'; ?></p>
        <div class="social-links">
          <?php if(!empty($S['social']['facebook'])): ?><a href="<?php echo esc($S['social']['facebook']); ?>" target="_blank" rel="noopener" aria-label="Facebook">f</a><?php endif; ?>
          <?php if(!empty($S['social']['twitter'])): ?><a href="<?php echo esc($S['social']['twitter']); ?>" target="_blank" rel="noopener" aria-label="Twitter">𝕏</a><?php endif; ?>
          <?php if(!empty($S['social']['instagram'])): ?><a href="<?php echo esc($S['social']['instagram']); ?>" target="_blank" rel="noopener" aria-label="Instagram">ig</a><?php endif; ?>
          <?php if(!empty($S['social']['linkedin'])): ?><a href="<?php echo esc($S['social']['linkedin']); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">in</a><?php endif; ?>
        </div>
      </div>
      <div>
        <h3 class="mt-0"><?php echo $lang==='ar'?'خدماتنا':'Services'; ?></h3>
        <ul>
          <li><a href="/services.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'جميع الخدمات':'All Services'; ?></a></li>
          <li><a href="/pricing.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الأسعار':'Pricing'; ?></a></li>
          <li><a href="/works.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'أعمالنا':'Portfolio'; ?></a></li>
          <li><a href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'طلب مشروع':'Start Project'; ?></a></li>
        </ul>
      </div>
      <div>
        <h3 class="mt-0"><?php echo $lang==='ar'?'روابط مهمة':'Company'; ?></h3>
        <ul>
          <li><a href="/about.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'من نحن':'About Us'; ?></a></li>
          <li><a href="/blog.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'المدونة':'Blog'; ?></a></li>
          <li><a href="/faq.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'الأسئلة الشائعة':'FAQ'; ?></a></li>
          <li><a href="/contact.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'اتصل بنا':'Contact'; ?></a></li>
        </ul>
      </div>
      <div>
        <h3 class="mt-0"><?php echo $lang==='ar'?'تواصل معنا':'Get in Touch'; ?></h3>
        <p class="m-0"><?php echo $lang==='ar'?'البريد':'Email'; ?>: <a href="mailto:<?php echo esc($S['contact_email']); ?>"><?php echo esc($S['contact_email']); ?></a></p>
        <p class="m-0"><?php echo $lang==='ar'?'الهاتف':'Phone'; ?>: <a href="tel:<?php echo esc($S['phone']); ?>"><?php echo esc($S['phone']); ?></a></p>
        <p class="m-0"><?php echo esc(t($S['hours'])); ?></p>
        <a class="btn btn-primary btn-sm mt-1" href="/brief.php?lang=<?php echo esc($lang); ?>"><?php echo $lang==='ar'?'ابدأ مشروعك':'Start Your Project'; ?></a>
      </div>
    </div>
    <div class="copy">&copy; <script>document.write(new Date().getFullYear())</script> <?php echo esc($S['site_name']); ?>. <?php echo $lang==='ar'?'جميع الحقوق محفوظة.':'All rights reserved.'; ?></div>
  </div>
</footer>
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<!-- Client Push Notification UI -->
<style>
/* ── Client Push Permission Banner ── */
.client-push-banner{display:none;position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:9000;
  background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;padding:.85rem 1.25rem;border-radius:14px;
  box-shadow:0 8px 30px rgba(37,99,235,.35);align-items:center;gap:.75rem;font-size:.85rem;font-family:inherit;
  max-width:460px;width:calc(100% - 2rem);animation:clientBannerSlide .5s ease}
@keyframes clientBannerSlide{from{opacity:0;transform:translateX(-50%) translateY(20px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}
.client-push-banner .cpb-icon{font-size:1.5rem;flex-shrink:0}
.client-push-banner .cpb-text{flex:1}
.client-push-banner .cpb-text strong{display:block;font-size:.9rem;margin-bottom:2px}
.client-push-banner .cpb-text span{font-size:.75rem;opacity:.85}
.client-push-banner .cpb-btns{display:flex;gap:.4rem;flex-shrink:0}
.client-push-banner .cpb-btns button{border:none;border-radius:8px;padding:.45rem .9rem;font-family:inherit;font-size:.78rem;font-weight:600;cursor:pointer}
.client-push-banner .btn-allow{background:#fff;color:#2563eb}
.client-push-banner .btn-allow:hover{background:#e0e7ff}
.client-push-banner .btn-dismiss{background:rgba(255,255,255,.15);color:#fff}
.client-push-banner .btn-dismiss:hover{background:rgba(255,255,255,.25)}
/* ── Client Toast Container ── */
.client-toast-container{position:fixed;bottom:1rem;right:1rem;z-index:9500;display:flex;flex-direction:column-reverse;gap:.5rem;max-width:380px;pointer-events:none}
.client-toast{pointer-events:auto;display:flex;align-items:flex-start;gap:.6rem;padding:.85rem 1rem;
  background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.15);
  animation:clientToastIn .35s ease;min-width:280px;border-right:3px solid #2563eb}
[data-theme="dark"] .client-toast{background:#1e293b;border-color:#334155;color:#f1f5f9;border-right-color:#7c3aed}
.client-toast-icon{font-size:1.3rem;flex-shrink:0}
.client-toast-body{flex:1;min-width:0}
.client-toast-body strong{font-size:.85rem;display:block;margin-bottom:3px}
.client-toast-body p{font-size:.75rem;color:#64748b;margin:0;line-height:1.4}
[data-theme="dark"] .client-toast-body p{color:#94a3b8}
.client-toast-close{background:none;border:none;font-size:1.2rem;color:#94a3b8;cursor:pointer;padding:0}
.client-toast-exit{animation:clientToastOut .3s ease forwards}
@keyframes clientToastIn{from{transform:translateX(30px);opacity:0}to{transform:translateX(0);opacity:1}}
@keyframes clientToastOut{from{opacity:1}to{opacity:0;transform:translateX(30px)}}
/* ── Client Flash ── */
.client-notif-flash{position:fixed;inset:0;z-index:9999;pointer-events:none;display:none;
  background:radial-gradient(circle at center,rgba(124,58,237,.35),rgba(37,99,235,.15),transparent 70%)}
@keyframes clientFlashAnim{0%{opacity:1}25%{opacity:.3}50%{opacity:.9}75%{opacity:.2}100%{opacity:0}}
/* ── Client Shake ── */
@keyframes clientShake{0%,100%{transform:translateX(0)}10%,50%,90%{transform:translateX(-4px)}30%,70%{transform:translateX(4px)}}
.client-shake{animation:clientShake .6s ease}
@media(max-width:600px){
  .client-push-banner{flex-direction:column;text-align:center;gap:.5rem}
  .client-push-banner .cpb-btns{justify-content:center}
  .client-toast-container{left:.5rem;right:.5rem;max-width:none}
}
</style>

<!-- Client Push Permission Banner -->
<div class="client-push-banner" id="clientPushBanner">
  <div class="cpb-icon">🔔</div>
  <div class="cpb-text">
    <strong><?php echo $lang==='ar'?'لا تفوّت أي تحديث!':'Don\'t miss any updates!'; ?></strong>
    <span><?php echo $lang==='ar'?'فعّل الإشعارات لتصلك آخر الأخبار والعروض فوراً':'Enable notifications to get the latest news and offers instantly'; ?></span>
  </div>
  <div class="cpb-btns">
    <button class="btn-allow" id="clientPushAllow"><?php echo $lang==='ar'?'تفعيل':'Enable'; ?></button>
    <button class="btn-dismiss" id="clientPushDismiss"><?php echo $lang==='ar'?'لاحقاً':'Later'; ?></button>
  </div>
</div>

<!-- Client Toast Container -->
<div class="client-toast-container" id="clientToastContainer"></div>

<!-- Client Flash Overlay -->
<div class="client-notif-flash" id="clientNotifFlash"></div>

<script>window.VAPID_PUBLIC_KEY = '<?php echo get_vapid_keys()["public_key"]; ?>';</script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/chat.js"></script>
<script src="/assets/js/analytics.js"></script>
<script src="/assets/js/client-notifications.js"></script>
</body>
</html>
