## Money Input for Filament

## Supported Versions

- Laravel 10, 11, 12 and 13
- Filament 3, 4 and 5

## Installation

Require this package in your composer.json and update composer:

```bash
composer require michaeld555/filament-money
```

## Using

To create a money input use:

```php
use Michaeld555\FilamentMoney\MoneyInput;

MoneyInput::make('value')
    ->prefix('R$');
```

The legacy namespace remains available:

```php
use Michaeld555\SecureShell\MoneyInput;
```
