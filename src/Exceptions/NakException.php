<?php

namespace rohsyl\Salto\Exceptions;

use rohsyl\Salto\Response\Response;

class NakException extends SaltoException
{
    public function __construct(Response $response)
    {
        parent::__construct($response, 'Nak. Server refused the command', 11);
    }
}
