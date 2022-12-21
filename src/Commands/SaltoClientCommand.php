<?php

namespace rohsyl\Salto\Commands;

use Illuminate\Console\Command;
use rohsyl\Salto\Exceptions\SaltoErrorException;
use rohsyl\Salto\Messages\CheckInMobileMessage;
use rohsyl\Salto\Messages\CheckoutMessage;
use rohsyl\Salto\Messages\EncodeMobileMessage;
use rohsyl\Salto\SaltoClient;
use Carbon\Carbon;

class SaltoClientCommand extends Command
{

    protected $signature = 'salto:client';

    public function handle() {

        $this->info('Salto Socket Client [Started]');

        $client = new SaltoClient('5.tcp.eu.ngrok.io', 14072);
        $client->openSocketConnection();

        if(!$client->isReady()) return;

        echo "ready\n";


        $message = (new CheckInMobileMessage())
            ->forRoom('W10011')
            ->withAuthorizations(['1' => true])
            ->phone('+41774539943')
            ->from(Carbon::create(2022, 12, 21, 10, 30))
            ->to(Carbon::create(2022, 12, 30, 10, 30))
            ->by('CCA Sylvain Roh')
            ->withMessage('Welcome lorem impsum')
        ;
        /*
                $message = (new CheckoutMessage(+))
                    ->forRoom('W10011');
                */
        /*
        $message = (new ModifyMessage())
            ->fromRoom($booking->rental->salto_room_id)
            ->expireAt(Carbon::create(2022, 12, 12, 11,0));
        */

        echo "Message : " . $message->toString() . "\n";

        try {
            $response = $client->sendMessage(
                $message
            );

            dump('SUCCESS');
            dump('Req : ' . $response->getRequest()->toString());
            dd('Res : ' . $response->toString());

        }
        catch(SaltoErrorException $e) {

            dump('ERROR');
            dump($e->getMessage());
            dump('Req : ' . $e->getRequest()->toString());
            dd('Res : ' . $e->getResponse()->toString());
        }

        echo "end \n";

        // ... do something with
        // $response ...
    }
}
