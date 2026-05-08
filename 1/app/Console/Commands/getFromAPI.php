<?php

namespace App\Console\Commands;

use App\Models\Price;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('app:get-from-api')]
#[Description('Get data from api and save to database')]
class getFromAPI extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get("https://api.binance.com/api/v3/ticker/price",
        [
            "symbol"=>"BTCUSDT",
        ]);
        if ($response->successful()) {
            $data = $response->json();
            Price::create($data);
        }
    }
}
