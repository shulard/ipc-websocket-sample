<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Ratchet\App('127.0.0.1', 8080);
$app->route('/echo', new Ratchet\Server\EchoServer, ['*']);
$app->run();
