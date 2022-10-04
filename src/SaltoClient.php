<?php

namespace rohsyl\Salto;

use rohsyl\Salto\Message\Message;

class SaltoClient
{
    const STX = 0x02; // Start of text, indicates the start of a message
    const ETX = 0x03; // End of text, indicates the end of a message
    const ENQ = 0x05; // Enquiry about the PC interface being ready to receive a new message
    const ACK = 0x06; // Positive acknowledgement to a PMS message or enquiry
    const NAK = 0x15; // Negative acknowledgement to a PMS message or enquiry
    const LRC_SKIP = 0x0D; // Skip LRC, indicates to skip LRC check.

    const SEPARATOR = 0xB3; // Field separator

    private $endpoint;
    private $port;
    private $lrc_skip = false;

    private $socket;

    public function __construct(string $endpoint, int $port)
    {
        $this->endpoint = $endpoint;
        $this->port = $port;
    }

    public function skipLrc($lrc_skip = true) {
        $this->lrc_skip = $lrc_skip;

        return $this;
    }

    public function openSocketConnection() {
        $this->socket = new Socket($this->endpoint, $this->port);
        $this->socket->open();
    }

    public function isReady() {
        return $this->sendRequest(self::ENQ) === self::ACK;
    }

    public function sendRequest($frame) {

        $this->socket->write($frame);

        // TODO read response and decode it
        $response = $this->socket->read();

        //...

        return $response;
    }

    public function sendMessage(Message $message) {

        $message->skipLrc($this->lrc_skip);

        return $this->sendRequest($message->getFrame());
    }
}
