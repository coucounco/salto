<?php

namespace rohsyl\Salto\Commands;

use Illuminate\Console\Command;
use rohsyl\Salto\Messages\CheckInMobileMessage;
use rohsyl\Salto\Messages\EncodeMobileMessage;
use rohsyl\Salto\SaltoClient;
use Carbon\Carbon;

class SaltoClientCommand extends Command
{

    protected $signature = 'salto:client';

    public function handle() {



        $this->info('Salto Socket Client [Started]');

        $client = new SaltoClient('4.tcp.eu.ngrok.io', 17960);
        $client->openSocketConnection();

        if(!$client->isReady()) return; // TODO wait if not ready ?

        echo "ready\n";

        $message = (new CheckInMobileMessage())
            ->phone('+41774539943') // #1
            ->for('W10011'); // #2

        /*

        $message = (new EncodeMobileMessage())
            ->for($booking->rental->salto_room_id)
            ->phone($booking->guest->phone_number_e164)
            ->from($booking->from)
            ->to($booking->to)
            ->by('CoucouApp')
            ->withMessage('Welcome lorem impsum ...');

        $message = (new CheckoutMessage())
            ->forRoom($booking->rental->salto_room_id);

        $message = (new ModifyMessage())
            ->fromRoom($booking->rental->salto_room_id)
            ->expireAt(Carbon::create(2022, 12, 12, 11,0));
        */

        echo "Message : " . $message->toString() . "\n";

        $response = $client->sendMessage(
            $message
        );

        // ... do something with
        // $response ...
    }
}
