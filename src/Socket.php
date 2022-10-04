<?php

namespace rohsyl\Salto;

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
        if(!is_resource($this->socket)) onSocketFailure("Failed to create socket");

        $result = socket_connect($this->socket, $this->endpoint, $this->port);

        if($result) {
            onSocketFailure("Failed to connect to '.$this->endpoint.':'.$this->port.'", $this->socket);
        }
    }

    public function write($data) {
        return socket_write($this->socket, $data);
    }

    public function readByte() {
        return socket_read($this->socket, 1);
    }

    public function close() {
        socket_close($this->socket);
    }
}
