<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;

$loop = React\EventLoop\Factory::create();
$stdin = new React\Stream\ReadableResourceStream(STDIN, $loop);

$connector = new Ratchet\Client\Connector($loop);
$connection = $connector('ws://127.0.0.1:8080/chat')->then(
    function (WebSocket $conn) use ($stdin) {
        $conn->on('message', function (MessageInterface $msg) {
            echo "{$msg}\n";
        });

        $stdin->on('data', function (string $data) use ($conn) {
            $conn->send(rtrim($data, "\n"));
        });
    }, function (Throwable $e) {
        echo "Could not connect: {$e->getMessage()}\n";
    }
);

$loop->run();
