<?php

namespace rohsyl\Salto\Response;

use rohsyl\Salto\Salto;
use rohsyl\Salto\SaltoClient;

class Response
{
    public static function Ack() {
        return new self(SaltoClient::ACK);
    }

    public static function Nak() {
        return new self(SaltoClient::NAK);
    }

    protected $rawResponse;
    protected $checksum;

    public function __construct($rawResponse, $checksum = null)
    {
        $this->rawResponse = $rawResponse;
        $this->checksum = $checksum;
    }

    public function isAck() {
        return $this->rawResponse === SaltoClient::ACK;
    }

    public function isNak() {
        return $this->rawResponse === SaltoClient::NAK;
    }

    public function isMessage() {
        return $this->rawResponse[0] === SaltoClient::STX;
    }

    public function raw() {
        return $this->rawResponse;
    }
}
