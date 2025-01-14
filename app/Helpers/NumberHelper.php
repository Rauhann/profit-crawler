<?php

declare(strict_types=1);

namespace App\Helpers;

class NumberHelper
{
    public static function stringToFloat(string $string): float
    {
        $string = str_replace('.', '', $string);
        $string = str_replace(',', '.', $string);
        return floatval($string);
    }

    public static function millionsToBillions(string $string): float
    {
        $string = str_replace([' milhões', ','], ['', '.'], $string);
        $float = self::stringToFloat($string);
        return $float / 1000;
    }
}
