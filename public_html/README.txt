
proomnes — نسخة احترافية متعددة اللغات مع لوحة إدارة وSEO
===========================================================

✔ PHP + SQLite + JSON (بدون أُطر/Composer) — تعمل مباشرة على Hostinger.
✔ لغات غير محدودة (افتراضيًا: AR/EN/FR) مع hreflang وcanonical.
✔ SEO كامل: Meta/OG/Twitter + JSON‑LD + robots + sitemap.xml.
✔ صفحة "طلب مشروع" تحفظ في قاعدة بيانات وتُرسل إشعار بريد.
✔ لوحة إدارة: إعدادات، خدمات، باقات، FAQ، SEO وسايت ماب، والطلبات.

المجلدات المهمة
---------------
/index.php, /about.php, /services.php, /service.php, /pricing.php, /faq.php, /contact.php, /brief.php
/admin/*  — لوحة كاملة (install, login, settings, services, plans, faq, seo, leads)
/inc/*    — دوال مساعدة
/data/*   — ملفات JSON + قاعدة leads.sqlite (تُنشأ تلقائيًا)
/assets/* — CSS/JS/Images/Uploads

طريقة النشر على Hostinger
-------------------------
1) ادخل File Manager ➜ public_html.
2) ارفع **proomnes-pro.zip** ثم Extract.
3) زر **/admin/install.php** أول مرة لتعيين بريد وكلمة مرور المسؤول.
4) حدّث الإعدادات من لوحة **Settings** (اسم الموقع، البريد، النطاق، اللغات…).
5) (اختياري) من **SEO & Sitemap** حدّث العناوين والأوصاف ثم احفظ لتحديث sitemap.xml.
6) جرّب صفحة **/brief.php**؛ ستجد الطلبات في **Admin → Leads** ويمكن تصدير CSV.

ملاحظات
-------
- البريد يستخدم الدالة mail()؛ على بعض الخطط قد يحتاج تفعيل. سيتم تسجيل كل الرسائل في data/mail.log كنسخة.
- تأكد من صلاحيات الكتابة على مجلدات: /data و /assets/uploads.
- لتغيير الألوان، استبدل CSS أو أضف متغيرات في :root.
