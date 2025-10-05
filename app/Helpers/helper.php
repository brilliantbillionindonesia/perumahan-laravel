<?php

if (!function_exists('generateTransactionCode')) {
    function generateTransactionCode(string $housingId, $now): string
    {
        $last5 = strtoupper(substr($housingId, -5));
        $date  = $now->format('ymd');
        $time  = $now->format('His');

        // $idx   = str_pad($index, 3, '0', STR_PAD_LEFT);

        return sprintf("TRX-%s-%s-%s", $last5, $date, $time);
    }
}
