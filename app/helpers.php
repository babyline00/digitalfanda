<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting(string $group, string $key, $default = null)
    {
        return Setting::get($group, $key, $default);
    }
}

if (!function_exists('format_price')) {
    function format_price(int $cents, string $currency = 'USD'): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];
        $symbol = $symbols[$currency] ?? $currency . ' ';
        return $symbol . number_format($cents / 100, 2);
    }
}

if (!function_exists('generate_order_number')) {
    function generate_order_number(): string
    {
        $prefix = 'DF-';
        $random = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        return $prefix . $random;
    }
}

if (!function_exists('generate_license_key')) {
    function generate_license_key(string $prefix = 'LIC'): string
    {
        $parts = [];
        for ($i = 0; $i < 4; $i++) {
            $parts[] = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 5));
        }
        return $prefix . '-' . implode('-', $parts);
    }
}