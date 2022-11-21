<?php

namespace rohsyl\Salto\Exceptions;

use rohsyl\Salto\Response\Response;

class WrongChecksumException extends SaltoException
{
    public function __construct(Response $response)
    {
        parent::__construct($response, "Wrong checksum exception", 10);
    }
}
