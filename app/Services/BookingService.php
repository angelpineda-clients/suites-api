<?php

namespace App\Services;

class BookingService
{

  /**
   * Checks exists a reservation for a query with room_id included
   * @param mixed $query should include room_id result -> Booking::query()->where('room_id', $roomID)
   * @param mixed $initial_date value for check_in
   * @param mixed $final_date value for check_out
   * @return mixed
   */
  public function checkOverlap($query, $initialDate, $finalDate): mixed
  {
    return $query
      ->where(function ($query) use ($initialDate, $finalDate) {
        $query->whereDate('check_in', '<=', $initialDate)
          ->whereDate('check_out', '>', $finalDate);
      })->orWhere(function ($query) use ($initialDate) {
        $query->whereDate('check_in', '<=', $initialDate)->whereDate('check_out', '>', $initialDate);
      })
      ->orWhere(function ($query) use ($finalDate) {
        $query->whereDate('check_in', '<', $finalDate)->whereDate('check_out', '>=', $finalDate);
      })
      ->exists();
  }
}