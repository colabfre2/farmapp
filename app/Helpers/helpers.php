<?php

if (!function_exists('rupiah')) {
    function rupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
