<?php
namespace rohsyl\Salto;

class SaltoManager
{
    public function __construct()
    {
        // constructor body
    }

    public function getClient($endpoint, $port) {
        return new SaltoClient($endpoint, $port);
    }

    public function getSocket(SaltoClient $client) {
        return Socket::forClient($client);
    }
}
