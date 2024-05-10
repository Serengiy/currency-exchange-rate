<?php

namespace App\Http\Requests\Currency;

use App\DTOs\ChartsDataDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChartDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            //valid_base_currency custom validated method initiated at Providers/AppServiceProvider.php
            'base_currency' => 'sometimes|string|valid_base_currency:'.implode(',',config('currencies')),
            'target_currency' => 'required|string|exists:currencies,currency',
        ];
    }

    public function data()
    {
        return ChartsDataDTO::from($this->validated());
    }
}
