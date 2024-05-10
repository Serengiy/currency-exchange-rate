<?php

namespace App\Http\Requests\Currency;

use App\DTOs\ExchangeRateDTO;
use Illuminate\Foundation\Http\FormRequest;

class ExchangeRateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //valid_base_currency custom validated method initiated at Providers/AppServiceProvider.php
            'base_currency' => 'sometimes|string|valid_base_currency:'.implode(',',config('currencies')),
            'currency' => 'required|string|exists:currencies,currency',
            'rate_updated_at' => 'sometimes|date',
            'page' => 'sometimes|integer',
        ];
    }

    public function data()
    {
        return ExchangeRateDTO::from($this->validated());
    }
}
