<?php
// API endpoint to return the status of a specific lead (project brief) by ID.
// This is used by the homepage to show users the current state of their request.
require_once __DIR__.'/../inc/functions.php';
header('Content-Type: application/json');

// Sanitize and validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo json_encode(['error' => 'invalid_id']);
  exit;
}

try {
  $db = leads_db();
  $stmt = $db->prepare('SELECT status FROM leads WHERE id = ?');
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    echo json_encode(['error' => 'not_found']);
    exit;
  }
  // Return the raw status string. It can be 'new', 'in_progress', or 'done'.
  echo json_encode(['status' => $row['status']]);
} catch (Exception $e) {
  echo json_encode(['error' => 'server_error']);
}
?>