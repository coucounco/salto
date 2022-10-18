<?php

namespace Spatie\Skeleton\Tests;

use PHPUnit\Framework\TestCase;
use rohsyl\Salto\Message\EncodeMobileMessage;
use rohsyl\Salto\SaltoClient;
use Carbon\Carbon;

class SaltoTest extends TestCase
{
    /** @test */
    public function salto_test()
    {

        try {

            $client = new SaltoClient('salto.domain.net', 6667);
            $client->openSocketConnection();

            if(!$client->isReady()) return; // TODO wait if not ready ?

            $response = $client->sendMessage(
                (new EncodeMobileMessage())
                    ->phone('+41774539943') // #1
                    ->for('Room 101') // #2
                    ->from(Carbon::create(2022, 10, 10, 16, 0, 0)) // format : hh mm DDMMYY // #8
                    ->to(Carbon::create(2022, 10, 22, 11, 0, 0)) // #9
                    ->by('Coucouapp') // #10
                    ->withMessage('Hello from Coucou&Co') // #14
            );

            // ... do something with
            // $response ...
        }
        catch(\Exception $e) {

        }



    }
}
