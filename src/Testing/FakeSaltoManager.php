<?php

namespace rohsyl\Salto\Testing;

use rohsyl\Salto\SaltoClient;
use rohsyl\Salto\SaltoManager;

class FakeSaltoManager extends SaltoManager
{
    public function getSocket(SaltoClient $client)
    {
        return FakeSocket::forClient($client);
    }

    public function mockResponse(array $bytes)
    {

    }

    public function assertConnected()
    {

    }

    public function assertSent()
    {

    }

    public function expectException()
    {

    }
}
