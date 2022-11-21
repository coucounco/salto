<?php

namespace rohsyl\Salto\Messages;

class CheckoutMessage extends Message
{

    public $name = 'CO';
    public $countFields = 3;

    public function __construct()
    {
        parent::__construct();
        $this->withEncodeNumber('0');
    }

    public function withEncodeNumber($encodeNumber) {
        $this->putField(1, $encodeNumber);

        return $this;
    }

    public function forRoom($room) {
        $this->putField(2, $room);

        return $this;
    }
}
