<?php

namespace rohsyl\Salto\Messages;

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
class CheckInMobileMessage extends EncodeMobileMessage {
}
