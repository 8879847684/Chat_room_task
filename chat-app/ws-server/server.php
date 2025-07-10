<?php
//require __DIR__ . '/../vendor/autoload.php';

require 'C:/xampp/htdocs/full-chat-app/vendor/autoload.php';


require 'ChatServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(new WsServer(new ChatServer())),
    8080
);

$server->run();
?>