<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients = [];

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[$conn->resourceId] = $conn;
        $conn->room = null;
        $conn->user = null;
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if ($data['type'] === 'join') {
            $from->room = $data['room'];
            $from->user = $data['user'];
            $this->broadcastUserList($from->room);
        } elseif ($data['type'] === 'message') {
            $message = htmlspecialchars($data['message']);
            $user = $from->user;
            $room = $from->room;

            $pdo = new PDO('mysql:host=localhost;dbname=chat_app', 'root', '');
            $stmt = $pdo->prepare("INSERT INTO messages (room_id, user_id, message_text) VALUES (?, (SELECT id FROM users WHERE username = ?), ?)");
            $stmt->execute([$room, $user, $message]);

            foreach ($this->clients as $client) {
                if ($client->room === $room) {
                    $client->send(json_encode([
                        'type' => 'message',
                        'user' => $user,
                        'message' => $message,
                        'time' => date('H:i')
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        unset($this->clients[$conn->resourceId]);
        $this->broadcastUserList($conn->room);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    private function broadcastUserList($room) {
        $users = [];
        foreach ($this->clients as $client) {
            if ($client->room === $room && $client->user) {
                $users[] = $client->user;
            }
        }

        foreach ($this->clients as $client) {
            if ($client->room === $room) {
                $client->send(json_encode(['type' => 'users', 'users' => $users]));
            }
        }
    }
}
?>