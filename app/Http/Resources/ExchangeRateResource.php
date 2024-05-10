<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'base_currency' => $this->base_currency,
            'currency' => $this->currency,
            'rate' => $this->rate_value,
            'updated_at' => $this->rate_updated_at,
        ];
    }
}
