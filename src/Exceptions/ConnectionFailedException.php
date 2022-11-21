<?php

namespace rohsyl\Salto\Exceptions;

use Exception;

class ConnectionFailedException extends Exception
{
    public function __construct($endpoint, $port)
    {
        parent::__construct("Failed to connect to '.$endpoint.':'.$port.'", 5);
    }
}
