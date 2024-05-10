<?php

namespace App\DTOs;

use App\Traits\Makeable;
use Illuminate\Support\Carbon;

class ExchangeRateDTO
{
    use Makeable;

    public function __construct(
        public readonly ?string $base_currency,
        public readonly ?string $currency,
        public readonly ?string $rate_updated_at,
        public readonly ?int $page,
    )
    {
    }

    public static function from($data)
    {
        return self::make(
            base_currency:  $data['base_currency'] ?? 'USD',
            currency:  $data['currency'] ?? null,
            page:  $data['page'] ?? 1,
            rate_updated_at:  isset($data['rate_updated_at']) ? Carbon::parse($data['rate_updated_at']) : null,
        );
    }

}
