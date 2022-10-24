<?php

namespace rohsyl\Salto\Commands;

use Illuminate\Console\Command;
use rohsyl\Salto\Message\EncodeMobileMessage;
use rohsyl\Salto\SaltoClient;
use Carbon\Carbon;

class SaltoClientCommand extends Command
{

    protected $signature = 'salto:client';

    public function handle() {

        $this->info('Salto Socket Client [Started]');

        $client = new SaltoClient('127.0.0.1', 10001);
        $client->openSocketConnection();

        if(!$client->isReady()) return; // TODO wait if not ready ?

        echo "ready\n";

        $message =     (new EncodeMobileMessage())
            ->phone('+41774539943') // #1
            ->for('101'); // #2

        echo "Message : " . $message->toString() . "\n";

        $response = $client->sendMessage(
            $message
        );

        // ... do something with
        // $response ...
    }
}
