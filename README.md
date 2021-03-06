# Laravel Cashier extension for Stripe Card Issuing API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kwidoo/card-issuing.svg?style=flat-square)](https://packagist.org/packages/kwidoo/card-issuing)
[![Total Downloads](https://img.shields.io/packagist/dt/kwidoo/card-issuing.svg?style=flat-square)](https://packagist.org/packages/kwidoo/card-issuing)
![GitHub Actions](https://github.com/kwidoo/card-issuing/actions/workflows/main.yml/badge.svg)

This package extends Laravel Cashier functionality with Card Issuing function. In order to use this functionality you should enable it on Stripe's end.

Currently it is POC package, don't expect much of it.

## Installation

You can install the package via composer:

```bash
composer require kwidoo/card-issuing
```

## Usage

### Prepare your model to use Card Issuing:

```php
use Kwidoo\CashierCardIssuing\Contracts\Cardholder as ContractsCardholder;

class User extends Authenticatable implements ContractsCardholder
{
    use ContractsCardholder;
}
```

### Create Cardholder

First you should create a cardholder on Stripe. Cardholder is a resource that represents a person or business that can be used to create cards.

```php
$user->createAsCardholder([
    'phone_number' => '+11234567890',
    'billing' =>
    [
        'address' => [
            'line1' => '1 Main Street',
            // 'line2' => , //uncomment if you have one
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
        ]
    ]
    'type' => User::ACCOUNT_TYPE_INDIVIDUAL,// optional
    'status' => User::STATUS_ACTIVE, // optional
]);
```

if $stripeValidation property is set to true (default), the createAsCardholder() method uses validation rules defined in Cardholder traits getCardholderCreateRules() method. If property is set to false validation rules will not be used, but Stripe will still validate the request.

```php
protected $stripeValidation = true;
```

You can override this method to change validation rules or you can add $cardIssueValidationRules property to User model. Array of rules under 'cardholder_create' key will be used.

```php
protected $cardIssueValidationRules = [
    'cardholder_create' => [
        'name' => 'required',
        'email' => 'required|email',
        'phone_number' => 'required',
    ],
    ...
];
```

### Create Card

Eloquent Way to create virtual card

```php
$user->cards()->create();
```

Eloquent Way to create physical card. Remember, Stripe requires name, shipping address and delivery service to be set.

```php
$user->cards()->create([
    'type' => 'physical',
    'shipping' =>
    [
        'name' => 'John Doe',
        'service'=>'standard',
        'type' => 'individual',
        'address' => [
            'line1' => '1 Main Street',
            // 'line2' => , //uncomment if you have one
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'GB',
        ]
    ]]);
```

Another way to create cards

```php
    $user->cards->virtual()->create();
```

Or

```php
    $user->cards
        ->physical()
        ->standard()
        ->individual()
        ->shipping([
            'line1' => '1 Main Street',
            // 'line2' => , //uncomment if you have one
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'GB',
        ])
        ->name('John Doe')
        ->create();
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email oleg@pashkovsky.me instead of using the issue tracker.

## Credits

- [Oleg Pashkovsky](https://github.com/kwidoo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
