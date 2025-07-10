<?php
require '../db.php';
$room_id = $_GET['room_id'] ?? 0;
$stmt = $pdo->prepare("SELECT m.message_text, m.timestamp, u.username FROM messages m JOIN users u ON m.user_id = u.id WHERE m.room_id = ? ORDER BY m.timestamp ASC");
$stmt->execute([$room_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>