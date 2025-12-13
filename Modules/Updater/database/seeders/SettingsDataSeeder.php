<?php

namespace Modules\Updater\Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // New Settings Data
        $settings = [
            [
                'type' => 'payment',
                'sub_type' => 'sslcommerz',
                'title' => 'SSLCommerz Settings',
                'fields' => [
                    'active' => false,
                    'test_mode' => true,
                    'currency' => 'BDT',
                    'store_id' => '',
                    'store_password' => '',
                ],
            ],
            [
                'type' => 'payment',
                'sub_type' => 'razorpay',
                'title' => 'Razorpay Settings',
                'fields' => [
                    'active' => false,
                    'test_mode' => true,
                    'currency' => 'INR',
                    'api_key' => '',
                    'api_secret' => '',
                ],
            ],
            [
                'type' => 'payment',
                'sub_type' => 'offline',
                'title' => 'Offline Payment Settings',
                'fields' => [
                    'active' => false,
                    'payment_instructions' => "Please complete your payment using one of the following payment details below. After making the payment, please submit your transaction details on the next page.",
                    'payment_details' => "Please put your offline payment/bank information here",
                ],
            ],
            [
                'type' => 'auth',
                'sub_type' => 'recaptcha',
                'title' => 'Google Recaptcha',
                'fields' => [
                    'active' => false,
                    'site_key' => '',
                    'secret_key' => '',
                ],
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['sub_type' => $setting['sub_type']], // Search by sub_type
                $setting                              // Find or insert
            );
        }

        $system = Setting::where('type', 'system')->first();

        if ($system && !array_key_exists('selling_currency', $system->fields)) {
            $system->fields = array_merge($system->fields, ['selling_currency' => 'USD']);
            $system->save();
        }

        if ($system && !array_key_exists('global_style', $system->fields)) {
            $system->fields = array_merge($system->fields, ['global_style' => '']);
            $system->save();
        }

        if ($system && !array_key_exists('direction', $system->fields)) {
            $system->fields = array_merge($system->fields, ['direction' => 'none']);
            $system->save();
        }

        if ($system && !array_key_exists('theme', $system->fields)) {
            $system->fields = array_merge($system->fields, ['theme' => 'system']);
            $system->save();
        }

        if ($system && !array_key_exists('language_selector', $system->fields)) {
            $system->fields = array_merge($system->fields, ['language_selector' => true]);
            $system->save();
        }
    }
}
