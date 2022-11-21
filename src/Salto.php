<?php

namespace rohsyl\Salto;

use Illuminate\Support\Facades\Facade;
use rohsyl\Salto\Testing\FakeSaltoManager;

/**
 * @method static Socket getSocket(SaltoClient $client)
 * @method static SaltoClient getClient(string $endpoint, int $port)
 *
 * @see SaltoManager
 */
class Salto extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SaltoManager::class;
    }


    public static function fake()
    {
        $fake = new FakeSaltoManager();
        static::swap($fake);
        return $fake;
    }
}
