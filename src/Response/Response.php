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

    public function getCommandName() {
        return $this->rawResponse[1];
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

    public function getRawMessageArray() {
        return array_slice($this->rawResponse, 1, sizeof($this->rawResponse) - 2);
    }

    public function isError() {
        return in_array($this->rawResponse[1], SaltoClient::getErrors());
    }

    public function getErrorDescription() {
        return SaltoClient::getErrorDescription($this->rawResponse[0]);
    }

    public function raw() {
        return $this->rawResponse;
    }

    public function check() {
        if($this->checksum == SaltoClient::LRC_SKIP) return true;

        return $this->checksum == SaltoClient::computeLrc($this->getRawMessageArray());
    }
}
