<?php

if (!function_exists('get_currency_code')) {
    function get_currency_code(): string
    {
        $currency = (string) (session()->get('currency') ?? 'usd');
        return in_array($currency, ['usd', 'php'], true) ? $currency : 'usd';
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return get_currency_code() === 'php' ? 'PHP' : '$';
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount): string
    {
        return currency_symbol() . ' ' . number_format((float) $amount, 2);
    }
}
