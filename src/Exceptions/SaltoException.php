<?php

namespace rohsyl\Salto\Exceptions;

use rohsyl\Salto\Messages\Message;
use rohsyl\Salto\Response\Response;

class SaltoException extends \Exception
{
    private Response $response;

    public function __construct(Response $response, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getRequest() : ?Message
    {
        return $this->getResponse()->getRequest();
    }
}
