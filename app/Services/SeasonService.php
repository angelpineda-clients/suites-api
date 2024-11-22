<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SeasonService
{
  public function checkOverlap($query, $initial_date, $final_date)
  {
    return $query
      ->where(function ($query) use ($initial_date, $final_date) {
        $query->whereRaw('MONTH(initial_date) = MONTH(?) AND DAY(initial_date) <= DAY(?)', [$initial_date, $final_date])->
          whereRaw('MONTH(final_date) = MONTH(?) AND DAY(final_date) > DAY(?)', [$initial_date, $initial_date]);
      })
      ->where(function ($query) use ($initial_date, $final_date) {
        $query->whereRaw('MONTH(final_date) = MONTH(?) AND DAY(final_date) >= DAY(?)', [$final_date, $initial_date])
          ->whereRaw('MONTH(initial_date) = MONTH(?) AND DAY(initial_date) < DAY(?)', [$final_date, $final_date]);
      })->exists();
  }

}