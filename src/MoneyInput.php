<?php

namespace Michaeld555\SecureShell;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class MoneyInput extends TextInput
{

    protected string|int|float|null $initialValue = '0,00';

    protected int|null $precision = 2;

    protected function setUp(): void
    {
        $this
            ->prefix('R$')
            ->maxLength(13)
            ->extraAlpineAttributes(function () {
                return [
                    'x-mask:dynamic' => 'function() {
                        var precision = ' . $this->precision . ';
                        var money = $el.value;
                        var divisor = Math.pow(10, precision);

                        money = money.replace(/\D/g, \'\');
                        money = (parseFloat(money) / divisor).toLocaleString(\'pt-BR\', { minimumFractionDigits: precision, maximumFractionDigits: precision });
                        $el.value = money === \'NaN\' ? \'0,\' + \'0\'.repeat(precision) : money;
                    }',
                ];
            })
            ->dehydrateMask()
            ->default(0.00)
            ->formatStateUsing(fn ($state) => $state ? number_format(floatval($state), $this->precision, ',', '.') : $this->initialValue);
    }

    public function dehydrateMask(bool|\Closure $condition = true): static
    {

        if ($condition) {
            $this->dehydrateStateUsing(
                fn ($state): ?float => $state ?
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

    public function precision(null|int $precision = 2): static
    {
        $this->precision = $precision;

        $this->initialValue = '0,'.str_repeat('0', $precision);

        return $this;
    }

    public function initialValue(null|string|int|float|\Closure $value = '0,00'): static
    {
        $this->initialValue = $value;

        return $this;
    }

}
