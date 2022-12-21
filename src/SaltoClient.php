<?php

namespace rohsyl\Salto;

use rohsyl\Salto\Exceptions\NakException;
use rohsyl\Salto\Exceptions\SaltoErrorException;
use rohsyl\Salto\Exceptions\WrongChecksumException;
use rohsyl\Salto\Messages\Message;
use rohsyl\Salto\Response\Response;
use rohsyl\Salto\Utils\Convert;

/**
 *
 */
class SaltoClient
{
    const STX = 0x02; // Start of text, indicates the start of a message
    const ETX = 0x03; // End of text, indicates the end of a message
    const ENQ = 0x05; // Enquiry about the PC interface being ready to receive a new message
    const ACK = 0x06; // Positive acknowledgement to a PMS message or enquiry
    const NAK = 0x15; // Negative acknowledgement to a PMS message or enquiry
    const LRC_SKIP = 0x0D; // Skip LRC, indicates to skip LRC check.

    const SEPARATOR = 0xB3; // Field separator

    const DATE_FORMAT = 'Hidmy';

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
        $this->socket = Salto::getSocket($this);
        $this->socket->open();
    }

    public function isReady() {
        return $this->sendRequest([self::ENQ])->isAck();
    }

    public function sendRequest(Message|array $frame) : Response {

        $request = null;
        if($frame instanceof Message) {
            $request = $frame;
            $frame = $frame->getFrame();
        }

        $waitAnswer = !$this->isEnq($frame);

        echo 'wait for answer : ' . ($waitAnswer ? 'yes' : 'no') . "\n";

        // convert string array to binary string
        $frame = Convert::stringArrayToBinaryString($frame);
        echo $frame . "\n";

        echo "Frame sent : \n" . Convert::binaryStringToHexaString($frame) . "\n";

        $this->socket->write($frame);

        return $this->readResponse($waitAnswer, $request);
    }

    public function isEnq($frame) {
        return ($frame[0] ?? null) == self::ENQ;
    }

    public function readResponse($waitAnswer = true, $request = null) {

        $isFrame = false;

        $isBody = false;
        $body = null;
        $bodyFieldIndex = null;
        $rawResponse = null;

        $isChecksum = false;
        echo "reading...\n";
        do {
            $byte = $this->socket->readByte();
            $byte = Convert::binaryToDecimal($byte);
            //echo "Byte read : " . self::decimalToHexaString([$byte]) . "\n";

            if(isset($rawResponse)) {
                $rawResponse[] = $byte;
            }

            // are we already processing a frame ?
            if(!$isFrame) {

                if($byte == SaltoClient::ACK) {
                    echo "ack.\n";
                    if(!$waitAnswer) {
                        return Response::Ack();
                    }
                    // wait for the next byte stx
                    $isFrame = false;
                    continue;
                }
                if($byte == SaltoClient::NAK) {
                    echo "nak.\n";
                    // request wont be processed
                    $response = Response::Nak();
                    break;
                }

                // wait until we get a stx that means it's the start of a frame
                if ($byte === SaltoClient::STX) {
                    echo "stx.\n";
                    $isFrame = true;
                    $rawResponse = [];
                    $rawResponse[] = $byte;
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
                $response = new Response($rawResponse, $body, $checksum);
                if(isset($request)) {
                    $response->setRequest($request);
                }

                break;
            }

        } while (true);

        echo "Response read : " . $response->getCommandName() . "\n";

        if($response->isNak()) {
            throw new NakException($response);
        }

        if(!$response->check()) {
            throw new WrongChecksumException($response);
        }

        if($response->isError()) {
            throw new SaltoErrorException($response);
        }

        return $response;
    }

    public function sendMessage(Message $message) {

        $message->skipLrc($this->lrc_skip);

        $response = $this->sendRequest($message);

        return $response;
    }


    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Compute the LRC checksum for the given array of bytes
     * @param array $bytearray
     * @return int
     */
    public static function computeLrc(array $stringArray) {
        $frame = Convert::stringArrayToBinaryString($stringArray);
        $lrc = 0x00;
        foreach (str_split($frame) as $char) {
            $lrc ^= ord($char);
        }
        return $lrc;
    }

    private static $_errors = [
        'ES' => 'Syntax error. The received message from the PMS is not correct (unknown command, nonsense parameters, prohibited characters, etc.)',
        'NC' => 'No communication. The specified encoder does not answer (encoder is switched off, disconnected from the PC interface, etc.)',
        'NF' => 'No files. Database file in the PC interface is damaged, corrupted or not found.',
        'OV' => 'Overflow. The encoder is still busy executing a previous task and cannot accept a new one.',
        'EP' => 'Card error. Card not found or wrongly inserted in the encoder.',
        'EF' => 'Format error. The card has been encoded by another system or may be damaged.',
        'TD' => 'Unknown room. This error occurs when trying to encode a card for a non-existing room.',
        'ED' => 'Timeout error. The encoder has been waiting too long for a card to be inserted. The operation is cancelled.',
        'EA' => 'This error occurs when the PC interface cannot execute the ‘CC’ command (encode copies of a guest card) because the room is checked out.',
        'OS' => 'This error occurs when the requested room is out of service.',
        'EO' => 'The requested guest card is being encoded by another station.',
        'EG' => 'General error. When the resulting error is none of the above described, the PC interface returns an ‘EG’ followed by an encoder number (or phone number depending on the original request) and an error description.',
    ];

    public static function getErrors() : array {
        return array_keys(self::$_errors);
    }

    public static function getErrorDescription($error) {
        return self::$_errors[$error] ?? null;
    }
}
