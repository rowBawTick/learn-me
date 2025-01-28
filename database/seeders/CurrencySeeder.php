<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'GBP',
                'symbol' => '£',
                'name' => 'British Pound Sterling'
            ],
            [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'United States Dollar'
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro'
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
