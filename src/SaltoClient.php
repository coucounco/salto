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

    /**
     * @var Socket
     */
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

        $responseAcknowledgement = $this->readResponse();

        if($responseAcknowledgement->isAck()) {
            // server got the request and will process it
            $requestResponse = $this->readResponse();

            return $requestResponse;
        }
        else if ($responseAcknowledgement->isNak()) {
            // server can't process the request
            // throw error
        }
    }

    public function readResponse() {

        $isFrame = false;

        $isBody = false;
        $body = null;
        $bodyFieldIndex = null;

        $isChecksum = false;
        $checksum = null;
        do {
            $byte = $this->socket->readByte();


            if($byte === SaltoClient::ACK) {
                // request will be processed
                // return Response::Ack();
            }
            if($byte === SaltoClient::NAK) {
                // request wont be processed
                // return Response::Nak();
            }

            // are we already processing a frame ?
            if(!$isFrame) {
                // wait until we get a stx that means it's the start of a frame
                if ($byte === SaltoClient::STX) {
                    $isFrame = true;
                }
            }
            // are we already processing the body of the frame ?
            else if (!$isBody) {
                // the body is composed of many fields separated by the separator.
                // the length of the body can vary.

                // if we get the first separator after the stx then it means it's
                // the begining of the message body.
                // we can init the body array that will contain every fields.
                // and init the current field index to 0
                if($byte === SaltoClient::SEPARATOR && !isset($body)) {
                    $body = [];
                    $bodyFieldIndex = 0;
                }
                // if we get another separator it's that we have retrived every bytes for the current field
                // and that we can start to retrive the next field bytes.
                // so we increment the field index by 1
                else if($byte === SaltoClient::SEPARATOR) {
                    $bodyFieldIndex++;
                }
                // if we get the etx it means that we got all fields of the message body
                else if($byte === SaltoClient::ETX) {
                    $isBody = true;
                }
                // otherwise retreive bytes for the current field index.
                else {
                    if(!isset($body[$bodyFieldIndex])) {
                        $body[$bodyFieldIndex] = [];
                    }
                    $body[$bodyFieldIndex][] = $byte;
                }

            }
            else if (!$isChecksum) {
                $checksum = $byte;
                break;
            }


        } while (true);

        $computedChecksum = self::computeLrc([

        ]);

        print_r([
            $body[0], // message name
            $body, // data
            $checksum, // checksum
        ]);

    }

    public function sendMessage(Message $message) {

        $message->skipLrc($this->lrc_skip);

        return $this->sendRequest($message->getFrame());
    }

    public static function computeLrc(array $bytearray) {
        $lrc = 0x00;
        foreach ($bytearray as $char) {
            $lrc ^= ord($char);
        }
        return $lrc;
    }
}
