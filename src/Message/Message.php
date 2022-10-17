<?php

namespace rohsyl\Salto\Message;

use rohsyl\Salto\SaltoClient;

class Message
{
    /**
     * @var string The name of the command
     */
    public $name;

    private $lrc_skip = false;

    /**
     * @var integer The number of fields of this command including the name
     */
    public $countFields;

    public $messageFields = [];

    public function __construct()
    {
        $this->initMessage();
    }

    public function initMessage() {
        $this->messageFields = array_fill(0, $this->countFields, null);
        $this->putField(0, $this->name);
    }

    public function getLrcChecksum() {
        $bytearray = [
            ...$this->getMessageArray(),
            SaltoClient::ETX,
        ];
        return SaltoClient::computeLrc($bytearray);
    }

    public function getFields() : array {
        return $this->messageFields;
    }

    public function getMessageArray() : array {

        $fields = $this->trimEmptyFields($this->messageFields);
        $bytearray = [];

        $bytearray[] = SaltoClient::SEPARATOR;
        foreach($fields as $field) {
            $bytearray = array_merge($bytearray, unpack('C*', $field));
            $bytearray[] = SaltoClient::SEPARATOR;
        }

        return $bytearray;
    }

    public function putField($index, $value) : void {
        $this->messageFields[$index] = $value;
    }

    /**
     * Remove empty fields from the end as they are not needed.
     * @return array
     */
    private function trimEmptyFields(array $fields) : array {

        for($i = sizeof($fields) - 1; $i >= 0; $i--) {
            $field = $fields[$i];
            if($field === null) {
                array_pop($fields);
            }
            else {
                break;
            }
        }

        return $fields;
    }

    public function skipLrc($lrc_skip = true) {
        $this->lrc_skip = $lrc_skip;
    }

    /**
     * Get the message frame as a decimal byte array
     * @return array
     */
    public function getFrame() : array {

        $lrc = $this->lrc_skip
            ? SaltoClient::LRC_SKIP
            : $this->getLrcChecksum();

        return [SaltoClient::STX, ...$this->getMessageArray(), SaltoClient::ETX, $lrc];
    }
}
