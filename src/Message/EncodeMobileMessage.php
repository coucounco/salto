<?php

namespace rohsyl\Salto\Message;

use rohsyl\Salto\SaltoClient;

class EncodeMobileMessage extends Message
{
    public $name = 'CNM';
    public $countFields = 16;

    /**
     * Set the phone number related to this message
     * @param  string  $phoneNumber
     * @return $this
     */
    public function phone(string $phoneNumber) {
        $this->putField(1, $phoneNumber);
        return $this;
    }

    public function for($rooms) {
        // if is string -> allow access to room
        if(is_string($rooms)) {
            $this->putField(2, $rooms);
        }
        // if array -> key = room and value is boolean that allow or deny access
        else if(is_array($rooms)) {
            $grantAccess = [];
            $denyAccess = [];
            $i = 0;
            foreach($rooms as $room => $hasAccess) {

                $this->putField(2 + $i, $room);
                $hasAccess
                    ? $grantAccess[] = $i + 1
                    : $denyAccess[] = $i + 1;

                $i++;
            }

            $this->putField();
        }

        return $this;
    }

    public function from(Carbon $date) {
        $this->putField(8, $date->format(SaltoClient::DATE_FORMAT));
        return $this;
    }

    public function to(Carbon $date) {
        $this->putField(9, $date->format(SaltoClient::DATE_FORMAT));
        return $this;
    }

    public function by(string $author) {
        $this->putField(10, $author);
        return $this;
    }

    public function withMessage(string $message) {
        $this->putField(14, $message);
        return $this;
    }
}
