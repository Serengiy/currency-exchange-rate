<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\ChartDataRequest;
use App\Http\Requests\Currency\ExchangeRateRequest;
use App\Http\Resources\ChartResource;
use App\Http\Resources\Currencty\IndexCurrencyResource;
use App\Http\Resources\ExchangeRateResource;
use App\Models\Currency;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $baseCurrencies = config('currencies');
        $data = collect();
        foreach ($baseCurrencies as $currency){
            $currencyRate = Currency::query()
                ->where('base_currency', 'USD')
                ->where('currency', $currency)
                ->latest()
                ->first();
            $data->push($currencyRate);
        }
        return IndexCurrencyResource::collection($data);
    }
    public function show(ExchangeRateRequest $request)
    {
        $data = $request->data();
        $availableCurrencies = config('currencies');

        $base_currency_code = $data->base_currency;
        $currency_code = $data->currency;

        if(!in_array($base_currency_code, $availableCurrencies)){
            return response()->json([
                'error'=> 'Wrong base currency'
            ], 400);
        }

        $exchange_rate = Currency::query()
            ->where('base_currency', $base_currency_code)
            ->where('currency', $currency_code)
            // retrieving smallest absolute difference  from available rows
            ->orderByRaw('ABS(EXTRACT(EPOCH FROM (rate_updated_at - ?)))', [$data->rate_updated_at])
            ->paginate(5);

        if($exchange_rate){
            return ExchangeRateResource::collection($exchange_rate);
        }

        // if no data found
        return response()->json([
            'error' => 'not found'
        ], 404);
    }

    public function charts(ChartDataRequest $request)
    {
        $data = $request->data();
        $data = Currency::query()
            ->whereBetween('rate_updated_at', [$data->start_date, $data->end_date])
            ->where('base_currency', $data->base_currency)
            ->where('currency', $data->target_currency)
            ->get();

        return ChartResource::collection($data);
    }
}
