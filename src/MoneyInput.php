<?php

namespace Michaeld555\SecureShell;

use Closure;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class MoneyInput extends TextInput
{
    
    protected string|int|float|Closure|null $initialValue = '0,00';

    protected ?int $precision = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->prefix('R$')
            ->maxLength(13)
            ->extraAlpineAttributes(fn (): array => [
                'x-mask:dynamic' => $this->getDynamicMask(),
            ])
            ->dehydrateMask()
            ->default(0.00)
            ->formatStateUsing(fn ($state): string => $this->formatState($state));
    }

    public function dehydrateMask(bool|Closure $condition = true): static
    {
        if ($this->evaluate($condition)) {
            $this->dehydrateStateUsing(
                fn ($state): ?float => $this->normalizeState($state)
            );
        } else {
            $this->dehydrateStateUsing(null);
        }

        return $this;
    }

    public function precision(?int $precision = 2): static
    {
        $this->precision = max(0, $precision ?? 2);

        $this->initialValue = $this->getZeroValue();

        return $this;
    }

    public function initialValue(string|int|float|Closure|null $value = '0,00'): static
    {
        $this->initialValue = $value;

        return $this;
    }

    protected function formatState(mixed $state): string
    {
        if ($state === null || $state === '') {
            return $this->resolveInitialValue();
        }

        return number_format((float) $state, $this->precision ?? 2, ',', '.');
    }

    protected function resolveInitialValue(): string
    {
        $initialValue = $this->evaluate($this->initialValue);

        if ($initialValue === null || $initialValue === '') {
            return $this->getZeroValue();
        }

        if (is_int($initialValue) || is_float($initialValue) || is_numeric($initialValue)) {
            return number_format((float) $initialValue, $this->precision ?? 2, ',', '.');
        }

        return (string) $initialValue;
    }

    protected function normalizeState(mixed $state): ?float
    {
        if ($state === null || $state === '') {
            return null;
        }

        return (float) Str::of((string) $state)
            ->replace('.', '')
            ->replace(',', '.')
            ->toString();
    }

    protected function getDynamicMask(): string
    {
        $precision = $this->precision ?? 2;
        $zeroValue = $this->getZeroValue();

        return <<<JS
        function() {
            var precision = {$precision};
            var money = \$el.value;
            var divisor = Math.pow(10, precision);

            money = money.replace(/\\D/g, '');
            money = (parseFloat(money) / divisor).toLocaleString('pt-BR', { minimumFractionDigits: precision, maximumFractionDigits: precision });
            \$el.value = money === 'NaN' ? '{$zeroValue}' : money;
        }
        JS;
    }

    protected function getZeroValue(): string
    {
        $precision = $this->precision ?? 2;

        if ($precision === 0) {
            return '0';
        }

        return '0,' . str_repeat('0', $precision);
    }
}
