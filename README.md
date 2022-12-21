# A PHP Salto client implementing the PMS Industry Standard protocol via TCP/IP.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rohsyl/salto.svg?style=flat-square)](https://packagist.org/packages/rohsyl/salto)
[![Total Downloads](https://img.shields.io/packagist/dt/rohsyl/salto.svg?style=flat-square)](https://packagist.org/packages/rohsyl/salto)


A PHP Salto client implementing the PMS Industry Standard protocol via TCP/IP.

## Installation

You can install the package via composer:

```bash
composer require rohsyl/salto
```

## Usage

Create a client
```php
$client = new SaltoClient('5.tcp.eu.ngrok.io', 14072);
```

Try to open the connection
```php
$client->openSocketConnection();
```
> Throws `ConnectionFailedException` when failed.

Check if the server is available
```php
while (!$client->isReady()) {
    // wait some seconds and try again
    // or exit program
}
```

Send a message
```php
$response = $client->sendMessage($message);
```

### Messages available

Only the followings message have been implemented yet :
- CNM : `CheckInMobileMessage`
- CCM : `CopyMobileMessage`
- MC  : `ModifyMessage`
- CO  : `CheckoutMessage`

#### CNM : Check-in Mobile

**Check-in for mobile apps.**

Create access to a room for a guest with a phone number for given dates
```php
$message = (new CheckInMobileMessage())
    ->forRoom('W10011')
    ->phone('+41774539943')
    ->from(Carbon::create(2022, 12, 21, 10, 30))
    ->to(Carbon::create(2022, 12, 30, 10, 30))
    ;
```

You can specify the operator
```php
$message = (new CheckInMobileMessage())
    // ...
    ->by('Firstname Lastname')
    ;
```

You can set a text message to be shown on the phone’s display.
```php
$message = (new CheckInMobileMessage())
    // ...
    ->withMessage('Lorem impsum')
    ;
```

You can allow or deny access to doors.
You have to pass an array in parameter :
 - **Key** is the ID of the PMS Authorizations (See System > PMS Authorizations). 
 - **Value** can be `true` or `false`.
```php
$message = (new CheckInMobileMessage())
    // ...
    ->withAuthorizations(['1' => true])
    ;
```

#### CCM : Copy Mobile

Works the same way as CNM : `CheckInMobileMessage`.

#### MC : Modify

Modify a check in. Changing the expiry dates.

```php
$message = (new ModifyMessage())
    ->fromRoom('W10011')
    ->expireAt(Carbon::create(2022, 12, 12, 11,0));
```
> An error will be returned if the room is checked-out or not occupied.

#### CO : Check-out

Check-out a room.

```php
$message = (new CheckoutMessage(+))
    ->forRoom('W10011');
```

### Read response

An instance of `Response` is returned by the `sendMessage` method.
```php
$response = $client->sendMessage($message);
```

If no exceptions is thrown, it means that everything went fine.


Display the string representation of the response and the request
```php
$response->toString();
$response->getRequest()->toString();
```

### Handle exceptions

Use a try .. catch !

```php
try {

}
catch(SaltoException $e) {
    if($e instanceof ConnectionFailedException) {
        // ...
    }
    else if($e instanceof NakException) {
        // ...
    }
    else if($e instanceof WrongChecksumException) {
        // ...
    }
    else if($e instanceof SaltoErrorException) {
        // ...
    }
}
```

Existing exceptions :

- `ConnectionFailedException` : Connection failed to the socket 
- `NakException` : Server responded with a negative acknowledgement. It means the server is not ready or the message sent is not correct (bad LRC)
- `WrongChecksumException` : Response message is not correct (bad lrc)
- `SaltoErrorException` : An error has been thrown by the server : See Error messages in PMS_SALTO_IS_V1.pdf

## Testing

TODO

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email syzin12@gmail.com instead of using the issue tracker.

## Credits

- [rohsyl](https://github.com/rohsyl)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
