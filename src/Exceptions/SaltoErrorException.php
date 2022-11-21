<?php

namespace rohsyl\Salto\Exceptions;

use rohsyl\Salto\Response\Response;

class SaltoErrorException extends SaltoException
{
    public function __construct(Response $response)
    {
        parent::__construct(
            $response,
            'Error : ' . $response->getCommandName() . ' : ' . $response->getErrorDescription(),
            12
        );
    }

    public function getErrorName() : string {
        return $this->getResponse()->getCommandName();
    }

    public function getErrorDescription() : ?string {
        return $this->getResponse()->getErrorDescription();
    }
}
