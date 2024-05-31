<?php

namespace Michaeld555\SecureShell;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class MoneyInput extends TextInput
{
    protected string|int|float|null $initialValue = '0,00';

    protected function setUp(): void
    {
        $this
            ->prefix('R$')
            ->maxLength(13)
            ->extraAlpineAttributes([
                'x-mask:dynamic' => 'function() {
                    var money = $el.value;
                    money = money.replace(/\D/g, \'\');
                    money = (parseFloat(money) / 100).toLocaleString(\'pt-BR\', { minimumFractionDigits: 2 });
                    $el.value = money === \'NaN\' ? \'0,00\' : money;
                }',
            ])
            ->prefix('R$')
            ->maxLength(13)
            ->dehydrateMask()
            ->default(0.00)
            ->formatStateUsing(fn($state) => $state ? number_format(floatval($state), 2, ',', '.') : $this->initialValue);
    }

    public function dehydrateMask(bool|\Closure $condition = true): static
    {

        if ($condition) {
            $this->dehydrateStateUsing(
                fn($state): ?float => $state ?
                floatval(
                    Str::of($state)
                        ->replace('.', '')
                        ->replace(',', '.')
                        ->toString()
                ) :
                null
            );
        } else {
            $this->dehydrateStateUsing(null);
        }

        return $this;
    }

    public function initialValue(null|string|int|float|\Closure $value = '0,00'): static
    {
        $this->initialValue = $value;

        return $this;
    }
}
