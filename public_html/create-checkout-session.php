<?php
require __DIR__.'/inc/functions.php'; csrf_verify();
$S = get_settings(); if(empty($S['payments']['stripe_secret'])){ header('Location: /payment.php'); exit; }
$PL = load_plans(); $id = $_POST['plan_id'] ?? '';
$plan = null; foreach($PL as $p){ if($p['id']===$id){ $plan=$p; break; } }
if(!$plan){ header('Location: /payment.php'); exit; }
$label = t($plan['name']).' '.($plan['price'] ?? '');
$price_raw = preg_replace('/[^0-9.]/','',$plan['price']); if($price_raw==='') $price_raw='0';
$amount_cents = (int)round(floatval($price_raw)*100);
$url = stripe_create_checkout($label, $amount_cents, $S['payments']['currency'] ?? 'usd');
if($url){ header('Location: '.$url); exit; } else { echo 'Stripe error. Check logs.'; }
