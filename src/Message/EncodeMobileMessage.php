<?php

namespace rohsyl\Salto\Message;

use rohsyl\Salto\SaltoClient;
use Carbon\Carbon;

/**
 * CNM Command
 * Salto locks are designed to support not only card-based contactless technologies
 * (such as Mifare) but also phone-based ones (such as BLE or NFC). The advantage
 * of BLE, for instance, is that you may upload (over the air) a given smart-phone with
 * the appropriate access permissions data and use this device to open doors as if it
 * were a conventional proximity card.
 */
class EncodeMobileMessage extends Message
{
    public $name = 'CNM';
    public $countFields = 16;

    /**
     * Telephone number of the target smart phone.
     * Use E164 format !
     * @param  string  $phoneNumber
     * @return $this
     */
    public function phone(string $phoneNumber) {
        $this->putField(1, $phoneNumber);
        return $this;
    }

    /**
     * You can either pass a string to give access to a given room or an array of room.
     * When passing an array you can precise if access is granted or denied.
     *
     * Exemple 1 :
     * ```
     *  $message->for('Room101');
     * ```
     *
     * Exemple 2 :
     * ```
     *  $message->for([
     *      'Room101' =>  true,
     *      'Parking => false,
     *  ]);
     * ```
     *
     *
     * @param string|array $rooms
     * @return $this
     */
    public function for(string|array $rooms) {
        // if is string -> allow access to a single room
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

            $this->putField(7, join('', $grantAccess));
            $this->putField(8, join('', $denyAccess));
        }

        return $this;
    }

    /**
     * Starting date and time of the card.
     * @param Carbon $date
     * @return $this
     */
    public function from(Carbon $date) {
        $this->putField(8, $date->format(SaltoClient::DATE_FORMAT));
        return $this;
    }

    /**
     * Expiring date and time of the card.
     * @param Carbon $date
     * @return $this
     */
    public function to(Carbon $date) {
        $this->putField(9, $date->format(SaltoClient::DATE_FORMAT));
        return $this;
    }

    /**
     * Data of the operator who makes the request. Max. 24 characters.
     * @param string $author
     * @return $this
     */
    public function by(string $author) {
        $this->putField(10, $author);
        return $this;
    }

    /**
     * Text message to be shown on the phone’s display. Max. 256 characters.
     * @param string $message
     * @return $this
     */
    public function withMessage(string $message) {
        $this->putField(14, $message);
        return $this;
    }

    public function authCode($code) {
        $this->putField(15, $code);
        return $this;
    }
}
