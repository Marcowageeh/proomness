<?php
// api/chat.php — proxy to OpenAI using settings in data/ai.json
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/../inc/functions.php';

// Simple rate limiting: max 10 requests per minute per IP
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rateFile = DATA_DIR . '/rate_' . md5($ip) . '.json';
$now = time();
$rateData = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) : ['count' => 0, 'reset' => $now + 60];
if ($now > ($rateData['reset'] ?? 0)) { $rateData = ['count' => 0, 'reset' => $now + 60]; }
$rateData['count']++;
file_put_contents($rateFile, json_encode($rateData));
if ($rateData['count'] > 10) {
  http_response_code(429);
  echo json_encode(['error' => 'rate_limit', 'message' => 'Too many requests. Try again later.']);
  exit;
}

$AI = get_ai_settings();
$raw = file_get_contents('php://input');
$in = json_decode($raw, true) ?: [];
$q = trim($in['q'] ?? $in['message'] ?? '');
if(!$q){ echo json_encode(['error'=>'empty']); exit; }
if(empty($AI['api_key'])){ echo json_encode(['answer'=>'(Set your OpenAI API key in Admin → Integrations → AI)']); exit; }
$messages = [
  ['role'=>'system','content'=>$AI['system_prompt'] ?: 'You are a helpful assistant for the website.'],
  ['role'=>'user','content'=>$q],
];
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch,[
  CURLOPT_POST=>true,
  CURLOPT_HTTPHEADER=>[
    'Content-Type: application/json',
    'Authorization: Bearer '.$AI['api_key']
  ],
  CURLOPT_POSTFIELDS=>json_encode([
    'model'=>$AI['model'] ?: 'gpt-4o-mini',
    'messages'=>$messages,
    'temperature'=>floatval($AI['temperature'] ?? 0.7),
    'max_tokens'=>intval($AI['max_tokens'] ?? 400),
  ], JSON_UNESCAPED_UNICODE),
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_TIMEOUT=>20
]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($resp === false || $http>=400){
  echo json_encode(['answer'=>'AI backend error']); exit;
}
$j = json_decode($resp, true);
$ans = $j['choices'][0]['message']['content'] ?? '';
echo json_encode(['answer'=>$ans,'reply'=>$ans]);
