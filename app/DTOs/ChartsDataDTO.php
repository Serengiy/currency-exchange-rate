<?php

namespace App\DTOs;

use App\Traits\Makeable;
use Illuminate\Support\Carbon;

class ChartsDataDTO
{
    use Makeable;

    public function __construct(
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly ?string $base_currency,
        public readonly string $target_currency,
    )
    {
    }

    public static function from($data)
    {
        return self::make(
            start_date:  Carbon::parse($data['start_date']),
            end_date:  Carbon::parse($data['end_date']),
            base_currency:  $data['base_currency'] ?? 'USD',
            target_currency:  $data['target_currency'],
        );
    }

}
