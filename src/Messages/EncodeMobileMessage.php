<?php

namespace rohsyl\Salto\Messages;

use rohsyl\Salto\SaltoClient;
use Illuminate\Support\Str;
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
     *      'Room101',
     *      'Parking',
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
        // if array -> allow access to many rooms
        else if(is_array($rooms)) {
            $i = 0;
            foreach($rooms as $room) {
                $this->putField(2 + $i, $room);
                $i++;
            }

        }

        return $this;
    }

    /**
     * With autho
     * @param array $authorizations
     * @return $this
     */
    public function withAuthorizations(array $authorizations) {
        $grantAccess = [];
        $denyAccess = [];
        foreach($authorizations as $authSymbol => $granted) {
            $granted
                ? $grantAccess[] = $authSymbol
                : $denyAccess[] = $authSymbol;
        }
        $this->putField(6, (isset($grantAccess) && !empty($grantAccess) ? join('', $grantAccess) : null));
        $this->putField(7, (isset($denyAccess) && !empty($denyAccess) ? join('', $denyAccess) : null));

        return $this;
    }

    public function forRoom(string $room) {
        return $this->for($room);
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
        $this->putField(10, Str::limit($author, 24, ''));
        return $this;
    }

    /**
     * Text message to be shown on the phone’s display. Max. 256 characters.
     * @param string $message
     * @return $this
     */
    public function withMessage(string $message) {
        $this->putField(14, Str::limit($message, 256, ''));
        return $this;
    }

    public function authCode($code) {
        $this->putField(15, Str::limit($code, 64, ''));
        return $this;
    }
}
