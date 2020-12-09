<?php

namespace CHStudio\IPC\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{
    private \SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn, new \stdClass);
        echo sprintf("[server] %s connected.\n", spl_object_hash($conn));
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        if (0 === strpos($message, '/name')) {
            $this->clients[$from]->name = trim(substr($message, 5));
            $answer = sprintf('[server] your name is now: %s', $this->clients[$from]->name);
            echo $answer . "\n";
            $from->send($answer);
            return;
        }

        if (isset($this->clients[$from]->name)) {
            $message = sprintf('%s: %s', $this->clients[$from]->name, $message);
        }

        echo $message . "\n";

        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($message);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo sprintf("[server] %s disconnected.\n", spl_object_hash($conn));
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
        echo sprintf("[error] %s\n", $e->getMessage());
    }
}
