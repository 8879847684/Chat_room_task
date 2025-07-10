<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat Room</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 10px; }
        #chat { list-style-type: none; max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
        #users { list-style-type: none; max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; }
        .container { display: flex; gap: 20px; }
    </style>
</head>
<body>
    <h3>Welcome, <?= $_SESSION['username'] ?></h3>
    <select id="room" onchange="switchRoom()"></select>
    <div class="container">
        <div style="flex: 3">
            <ul id="chat"></ul>
            <input id="message" placeholder="Type a message" />
            <button onclick="sendMessage()">Send</button>
        </div>
        <div style="flex: 1">
            <h4>Online Users</h4>
            <ul id="users"></ul>
        </div>
    </div>

    <script>
        let currentRoom = null;
        const socket = new WebSocket("ws://localhost:8080");

        socket.onopen = () => fetchRooms();

        socket.onmessage = event => {
            const data = JSON.parse(event.data);
            if (data.type === 'message') {
                const li = document.createElement('li');
                li.textContent = `[${data.time}] ${data.user}: ${data.message}`;
                document.getElementById('chat').appendChild(li);
            } else if (data.type === 'users') {
                const userList = document.getElementById('users');
                userList.innerHTML = '';
                data.users.forEach(u => {
                    const li = document.createElement('li');
                    li.textContent = u;
                    userList.appendChild(li);
                });
            }
        };

        function fetchRooms() {
            fetch('rooms.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('room');
                    data.forEach(room => {
                        const opt = document.createElement('option');
                        opt.value = room.id;
                        opt.text = room.name;
                        select.appendChild(opt);
                    });
                    if (data.length) {
                        currentRoom = data[0].id;
                        select.value = currentRoom;
                        joinRoom();
                    }
                });
        }

        function joinRoom() {
            document.getElementById('chat').innerHTML = '';
            fetch('messages.php?room_id=' + currentRoom)
                .then(res => res.json())
                .then(messages => {
                    messages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = `[${msg.timestamp}] ${msg.username}: ${msg.message_text}`;
                        document.getElementById('chat').appendChild(li);
                    });
                });
            socket.send(JSON.stringify({ type: 'join', room: currentRoom, user: "<?= $_SESSION['username'] ?>" }));
        }

        function switchRoom() {
            currentRoom = document.getElementById('room').value;
            joinRoom();
        }

        function sendMessage() {
            const msg = document.getElementById('message').value;
            socket.send(JSON.stringify({ type: 'message', room: currentRoom, user: "<?= $_SESSION['username'] ?>", message: msg }));
            document.getElementById('message').value = '';
        }
    </script>
</body>
</html>