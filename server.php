<?php

require dirname( __FILE__ ) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Sockett implements MessageComponentInterface {

    protected $clients;
    protected $clientChannel = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        if (!isset($this->clientChannel[$from->resourceId])) {
                $channelId = $msg;
                $this->clientChannel[$from->resourceId] = $channelId;
        } else {

            foreach ( $this->clients as $client ) {

                if ( $from->resourceId == $client->resourceId ) {
                    continue;
                }

                if ($this->clientChannel[$from->resourceId] == $this->clientChannel[$client->resourceId])
                    $client->send($msg);
            }

        }
    }

    public function onClose(ConnectionInterface $conn) {

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Sockett()
        )
    ),
    8080
);

$server->run();