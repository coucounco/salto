<?php

namespace rohsyl\Salto;

use Illuminate\Support\Facades\Facade;

/**
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
}
