<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CreditCard;

class CreditCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tarjetas de crédito
        CreditCard::create([
            'user_id' => 2,
            'token' => 'tok_test_visa_1',
            'last4' => '4242',
            'brand' => 'visa',
            'exp_month' => 12,
            'exp_year' => 2028,
            'holder_name' => 'Usuario 1',
            'is_default' => true,
        ]);

        CreditCard::create([
            'user_id' => 3,
            'token' => 'tok_test_mc_1',
            'last4' => '4444',
            'brand' => 'mastercard',
            'exp_month' => 7,
            'exp_year' => 2027,
            'holder_name' => 'Usuario 2',
            'is_default' => true,
        ]);
    }
}
