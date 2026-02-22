<?php
/**
 * Public Client Push Notification API
 * No admin auth required — used by website visitors
 *
 * Actions:
 *   subscribe   — Save a client push subscription
 *   unsubscribe — Remove a client push subscription
 *   latest      — Get latest client broadcast (within 5 minutes)
 */
require_once __DIR__ . '/../inc/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Rate limiting: max 30 requests per minute per IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = DATA_DIR . '/client_push_rate.json';
$rateData = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) : [];
$now = time();
// Clean old entries (older than 60 seconds)
foreach($rateData as $k => $v){
  if($v['time'] < $now - 60) unset($rateData[$k]);
}
$key = md5($ip);
if(isset($rateData[$key]) && $rateData[$key]['count'] >= 30){
  http_response_code(429);
  echo json_encode(['error' => 'Too many requests']);
  exit;
}
$rateData[$key] = ['time' => $now, 'count' => ($rateData[$key]['count'] ?? 0) + 1];
@file_put_contents($rateFile, json_encode($rateData));

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action){

  case 'subscribe':
    $input = json_decode(file_get_contents('php://input'), true);
    $endpoint = $input['endpoint'] ?? '';
    $p256dh   = $input['keys']['p256dh'] ?? ($input['p256dh'] ?? '');
    $auth     = $input['keys']['auth'] ?? ($input['auth'] ?? '');
    if(!$endpoint || !$p256dh || !$auth){
      http_response_code(400);
      echo json_encode(['error' => 'Missing subscription data']);
      exit;
    }
    save_client_push_subscription($endpoint, $p256dh, $auth);
    echo json_encode(['ok' => true, 'message' => 'Subscribed']);
    break;

  case 'unsubscribe':
    $input = json_decode(file_get_contents('php://input'), true);
    $endpoint = $input['endpoint'] ?? '';
    if($endpoint){
      remove_client_push_subscription($endpoint);
    }
    echo json_encode(['ok' => true, 'message' => 'Unsubscribed']);
    break;

  case 'latest':
    $broadcast = get_latest_client_broadcast(300); // within 5 minutes
    if($broadcast){
      echo json_encode(['ok' => true, 'broadcast' => $broadcast]);
    } else {
      echo json_encode(['ok' => true, 'broadcast' => null]);
    }
    break;

  default:
    http_response_code(400);
    echo json_encode(['error' => 'Unknown action']);
}
