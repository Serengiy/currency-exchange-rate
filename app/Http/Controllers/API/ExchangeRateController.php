<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\ChartDataRequest;
use App\Http\Requests\Currency\ExchangeRateRequest;
use App\Http\Resources\ChartResource;
use App\Http\Resources\Currencty\IndexCurrencyResource;
use App\Http\Resources\ExchangeRateResource;
use App\Models\Currency;
use App\services\ExchangeRateService;

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
        return ExchangeRateService::index();
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
        return ExchangeRateService::show($request->data());
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
        return ExchangeRateService::charts($request->validated());
    }
}
