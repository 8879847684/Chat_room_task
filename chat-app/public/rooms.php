<?php
require '../db.php';
$stmt = $pdo->query("SELECT id, name FROM chat_rooms");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>