<?php

namespace rohsyl\Salto\Response;

use rohsyl\Salto\Messages\Message;
use rohsyl\Salto\SaltoClient;
use rohsyl\Salto\Utils\Convert;

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
        return new self([SaltoClient::ACK], [SaltoClient::ACK], null);
    }

    /**
     * Instance a Nak response
     * @return Response
     */
    public static function Nak() {
        return new self([SaltoClient::NAK], [SaltoClient::NAK], null);
    }

    protected $request;

    /**
     * @var array The raw response sent by the server. Each row contains a field.
     */
    protected $rawResponse;

    protected $body;

    /**
     * @var string|null The checksum of the response
     */
    protected $checksum;

    public function __construct($rawResponse, $body, $checksum = null)
    {
        $this->rawResponse = $rawResponse;
        $this->body = $body;
        $this->checksum = $checksum;
    }

    /**
     * Get the command name that should be included in the response
     * @return mixed
     */
    public function getCommandName() {
        return Convert::decimalArrayToString($this->body[0]);
    }

    /**
     * Is Ack
     * @return bool
     */
    public function isAck() {
        return $this->rawResponse[0] === SaltoClient::ACK;
    }

    /**
     * Is Nak
     * @return bool
     */
    public function isNak() {
        return $this->rawResponse[0] === SaltoClient::NAK;
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
        // get response but without stx and checksum
        return array_slice($this->rawResponse, 1, sizeof($this->rawResponse) - 2);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Is this response an error response
     * @return bool
     */
    public function isError() {
        return in_array($this->getCommandName(), SaltoClient::getErrors());
    }

    /**
     * Get the text decription of the error (if there is one)
     * @return string|null
     */
    public function getErrorDescription() {
        return SaltoClient::getErrorDescription($this->getCommandName());
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

    public function toString() {
        $out = 'STX|';
        foreach($this->getBody() as $field) {
            $out .= Convert::decimalArrayToString($field) . '|';
        }
        return $out . 'ETX' . $this->checksum;
    }
}
