<?php

namespace rohsyl\Salto\Messages;

use Carbon\Carbon;
use rohsyl\Salto\SaltoClient;

class ModifyMessage extends Message
{
    public $name = 'MC';
    public $countFields = 5;

    public function fromRoom($room) {
        $this->putField(1, $room);
        return $this;
    }

    public function toRoom($room) {
        $this->putField(2, $room);
        return $this;
    }

    public function expireAt(Carbon $expiryDate) {
        $this->putField(3, $expiryDate->format(SaltoClient::DATE_FORMAT));
        return $this;
    }

    public function authCode($code) {
        $this->putField(4, $code);
        return $this;
    }
}
