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
    /**
     * Display a listing of available exchange rates.
     *
     * @group Exchange Rates
     * @authenticated
     * @response
     *   "data": [
     *     {
     *       "currency": "USD",
     *       "rate": 1
     *     },
     *     {
     *       "currency": "THB",
     *       "rate": 36.601
     *     }
     *   ]
     *
     */
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

    /**
     * Display the exchange rates for a specific currency.
     *
     * This endpoint retrieves exchange rates for a specific currency based on the provided base currency and rate update timestamp.
     *
     * @group Exchange Rates
     * @authenticated
     *
     * @bodyParam base_currency string optional USD default The base currency code. Example: USD
     * @bodyParam currency string required The target currency code. Example: THB
     * @bodyParam rate_updated_at string optional The timestamp of the rate update (in UTC) in the format "Y-m-d H:i:s".
     *
     * @response
     *   "data": [
     *     {
     *       "base_currency": "USD",
     *       "currency": "THB",
     *       "rate": 36.88,
     *       "rate_updated_at": "2024-05-12 08:00:00"
     *     },
     *     {
     *       "base_currency": "USD",
     *       "currency": "THB",
     *       "rate": 36.76,
     *       "rate_updated_at": "2024-05-12 08:01:00"
     *     }
     *   ]
     *
     *
     * @response status=400
     *   "error": "Wrong base currency"
     *
     *
     * @response status=404
     *   "error": "Not found"
     *
     */
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

        if($exchange_rate->isNotEmpty()){
            return ExchangeRateResource::collection($exchange_rate);
        }

        // if no data found
        return response()->json([
            'error' => 'not found'
        ], 404);
    }

    /**
     * Display exchange rate data for chart visualization.
     *
     * This endpoint retrieves exchange rate data for chart visualization based on the provided criteria.
     *
     * @group Exchange Rates
     * @authenticated
     *
     * @bodyParam start_date string required The start date for the data range (format: "Y-m-d").
     * @bodyParam end_date string required The end date for the data range (format: "Y-m-d").
     * @bodyParam base_currency string optional USD default The base currency code. Example: USD
     * @bodyParam target_currency string required The target currency code.
     *
     * @response
     *   "data": [
     *     {
     *       "date": "2024-05-01",
     *       "rate": 0.88
     *     },
     *     {
     *       "date": "2024-05-02",
     *       "rate": 0.90
     *     },
     *   ]
     *
     *
     * @response status=404
     *   "error": "No data available for the specified criteria"
     *
     * @response status=422
     *   "message": "The given data was invalid.",
     *   "errors":
     *       "start_date": ["The start date field is required."],
     *       "end_date": ["The end date field is required."],
     *       "base_currency": ["The selected base currency is invalid."],
     *       "target_currency": ["The selected target currency is invalid."]
     */

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
