<?php

namespace rohsyl\Salto\Response;

use rohsyl\Salto\Messages\Message;
use rohsyl\Salto\SaltoClient;

/**
 *
 */
class Response
{
    /**
     * Instance an Ack response
     * @return Response
     */
    public static function Ack() {
        return new self(SaltoClient::ACK);
    }

    /**
     * Instance a Nak response
     * @return Response
     */
    public static function Nak() {
        return new self(SaltoClient::NAK);
    }

    protected $request;

    /**
     * @var array The raw response sent by the server. Each row contains a field.
     */
    protected $rawResponse;

    /**
     * @var string|null The checksum of the response
     */
    protected $checksum;

    public function __construct($rawResponse, $checksum = null)
    {
        $this->rawResponse = $rawResponse;
        $this->checksum = $checksum;
    }

    /**
     * Get the command name that should be included in the response
     * @return mixed
     */
    public function getCommandName() {
        return $this->rawResponse[1];
    }

    /**
     * Is Ack
     * @return bool
     */
    public function isAck() {
        return $this->rawResponse === SaltoClient::ACK;
    }

    /**
     * Is Nak
     * @return bool
     */
    public function isNak() {
        return $this->rawResponse === SaltoClient::NAK;
    }

    /**
     * Is a response to a command/message sent.
     * @return bool
     */
    public function isMessage() {
        return $this->rawResponse[0] === SaltoClient::STX;
    }

    /**
     * Get the raw message array
     * @return array
     */
    public function getRawMessageArray() {
        return array_slice($this->rawResponse, 1, sizeof($this->rawResponse) - 2);
    }

    /**
     * Is this response an error response
     * @return bool
     */
    public function isError() {
        return in_array($this->rawResponse[1], SaltoClient::getErrors());
    }

    /**
     * Get the text decription of the error (if there is one)
     * @return string|null
     */
    public function getErrorDescription() {
        return SaltoClient::getErrorDescription($this->rawResponse[0]);
    }

    /**
     * Get the raw response
     * @return array
     */
    public function raw() {
        return $this->rawResponse;
    }

    /**
     * Return true if the checksum is valid
     * @return bool
     */
    public function check() {
        if($this->checksum == SaltoClient::LRC_SKIP) return true;

        return $this->checksum == SaltoClient::computeLrc($this->getRawMessageArray());
    }

    public function setRequest(Message $request): void
    {
        $this->request = $request;
    }

    public function getRequest() : Message
    {
        return $this->request;
    }
}
