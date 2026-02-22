<?php
// api/analytics.php — store anonymized events in data/analytics.jsonl
require_once __DIR__.'/../inc/functions.php';

// Rate limit: max 30 events per minute per IP
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rateFile = DATA_DIR . '/rate_an_' . md5($ip) . '.json';
$now = time();
$rd = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) : ['c' => 0, 'r' => $now + 60];
if ($now > ($rd['r'] ?? 0)) { $rd = ['c' => 0, 'r' => $now + 60]; }
$rd['c']++;
file_put_contents($rateFile, json_encode($rd));
if ($rd['c'] > 30) { http_response_code(429); exit; }

$raw = file_get_contents('php://input');
if(!$raw){ http_response_code(204); exit; }
$data = json_decode($raw, true);
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$hash = substr(hash('sha256',$ip.$_SERVER['HTTP_USER_AGENT']??''),0,16);
$entry = [
  'ts'=>date('c'),
  'evt'=>$data['evt'] ?? 'event',
  'url'=>$data['url'] ?? '',
  'data'=>$data['data'] ?? [],
  'ua'=>$data['ua'] ?? '',
  'uid'=>$hash,
];
file_put_contents(__DIR__.'/../data/analytics.jsonl', json_encode($entry, JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);
header('Content-Type: image/gif'); // tiny response for beacons
echo '';
