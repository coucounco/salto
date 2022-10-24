<?php

namespace rohsyl\Salto;

use rohsyl\Salto\Message\Message;

class Socket
{
    private $endpoint;
    private $port;

    private $socket;

    public function __construct(string $endpoint, int $port)
    {
        $this->endpoint = $endpoint;
        $this->port = $port;
    }

    public function open() {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        $result = socket_connect($this->socket, $this->endpoint, $this->port);

        if(!$result) {
            print_r("Failed to connect to '.$this->endpoint.':'.$this->port.'") and die();
        }
    }

    public function write($data) {
        return socket_write($this->socket, $data);
    }

    public function readByte() {
        return socket_read($this->socket, 1, PHP_BINARY_READ);
    }

    public function close() {
        socket_close($this->socket);
    }
}
