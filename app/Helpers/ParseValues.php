<?php

namespace App\Helpers;

class ParseValues
{

  public static function priceToCents($amount)
  {
    return number_format(num: $amount, decimals: 2, decimal_separator: '', thousands_separator: '');
  }

  public static function centsToPrice($number)
  {
    $amount = $number / 100;

    return number_format(num: $amount, decimals: 2, decimal_separator: '.', thousands_separator: ',');
  }
}