## Money Input for Filament

## Installation

Require this package in your composer.json and update composer. This will download the package.

    composer require michaeld555/filament-money
  
## Using

To create a money input use:

```php
    use Michaeld555\SecureShell\MoneyInput;

    MoneyInput::make('value')
    ->prefix('R$')