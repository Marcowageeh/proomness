<?php
/**
 * Notifications API — real-time polling + mark-as-read + push subscription
 * GET  ?action=poll&since=ID   → returns new notifications since ID + unread count
 * POST ?action=read            → body: {"ids":[1,2,3]} mark specific as read
 * POST ?action=read_all        → mark all as read
 * POST ?action=subscribe       → body: {"endpoint","p256dh","auth"} save push sub
 */
require_once __DIR__.'/../inc/functions.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

if (!admin_logged()) {
  http_response_code(401);
  echo json_encode(['error' => 'unauthorized']);
  exit;
}

$action = $_GET['action'] ?? 'poll';

try {
  switch($action){
    case 'poll':
      $since_id = (int)($_GET['since'] ?? 0);
      $notifications = get_notifications_since($since_id, 30);
      $unread = count_unread_notifications();
      // Also get new leads count for dashboard badge
      $db = leads_db();
      $newLeads = (int)$db->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn();
      echo json_encode([
        'unread' => $unread,
        'new_leads' => $newLeads,
        'notifications' => $notifications
      ]);
      break;

    case 'read':
      if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo json_encode(['error' => 'POST required']);
        break;
      }
      $input = json_decode(file_get_contents('php://input'), true);
      $ids = $input['ids'] ?? [];
      if(!empty($ids)){
        mark_notifications_read($ids);
      }
      echo json_encode(['ok' => true, 'unread' => count_unread_notifications()]);
      break;

    case 'read_all':
      if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo json_encode(['error' => 'POST required']);
        break;
      }
      mark_notifications_read();
      echo json_encode(['ok' => true, 'unread' => 0]);
      break;

    case 'subscribe':
      if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo json_encode(['error' => 'POST required']);
        break;
      }
      $input = json_decode(file_get_contents('php://input'), true);
      if(!empty($input['endpoint'])){
        save_push_subscription(
          $input['endpoint'],
          $input['p256dh'] ?? '',
          $input['auth'] ?? ''
        );
        echo json_encode(['ok' => true]);
      } else {
        http_response_code(400);
        echo json_encode(['error' => 'endpoint required']);
      }
      break;

    default:
      echo json_encode(['error' => 'unknown action']);
  }
} catch(Exception $e){
  http_response_code(500);
  echo json_encode(['error' => 'server_error']);
}
?>