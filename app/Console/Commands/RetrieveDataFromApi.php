<?php

namespace App\Console\Commands;

use App\Models\Currency;
use CurrencyApi\CurrencyApi\CurrencyApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RetrieveDataFromApi extends Command
{
    protected $signature = 'retrieve.data';

    public function handle()
    {
        $apiKey = config('api_keys.cur_key');
        $currencies =config('currencies');

        //lib provided by https://currencyapi.com/
        $currencyapi = new CurrencyApiClient($apiKey);

        $startDate = now()->subMinutes(2)->toIso8601String();
        $endDate = now()->toIso8601String();

        // requesting data for each currency from env file
        foreach ($currencies as $currency){
            $res = $currencyapi->range([
                'datetime_start' => $startDate,
                'datetime_end' => $endDate,
                'base_currency' => $currency,
                'accuracy' => 'minute'
            ]);
            foreach ($res["data"] as $curByDate){
                foreach ($curByDate['currencies'] as $code => $value) {
                    // firstOrCreate method prevents from duplicating data in case if requested data already stored in database
                    Currency::query()->firstOrCreate([
                        'base_currency'=> $currency,
                        'currency'=> $code,
                        'rate_updated_at' => Carbon::parse($curByDate['datetime'])
                    ],[
                        'rate_value' =>$value['value'],
                    ]);
                }
            }
        }

    }
}
