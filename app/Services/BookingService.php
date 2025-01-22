<?php

namespace App\Services;

use App\Models\Booking;
use DB;

class BookingService
{

  /**
   * Checks exists a reservation for a query with room_id included
   * @param mixed $roomID should include room_id result -> Booking::query()->where('room_id', $roomID)
   * @param mixed $initial_date value for check_in
   * @param mixed $final_date value for check_out
   * @return mixed
   */
  public function checkOverlap($query, $startDate, $endDate): mixed
  {
    return $query
      ->where(function ($query) use ($startDate, $endDate) {
        $query->where('check_in', '<=', $endDate)
          ->where('check_out', '>=', $startDate);
      })
      ->whereNot(function ($query) use ($startDate, $endDate) {
        $query->where('check_out', '=', $startDate) // Caso sin conflicto 1
          ->orWhere('check_in', '=', $endDate); // Caso sin conflicto 2
      })
      ->exists();
  }

  /**
   * Take two dates and return season with prices if exists between dates
   * @return void
   */
  public function roomPricesBySeason($roomId, $initialDate, $finalDate, $basePrice): string
  {
    $query = "
        WITH RECURSIVE date_range AS (
            SELECT :initial_date AS date
            UNION ALL
            SELECT DATE_ADD(date, INTERVAL 1 DAY)
            FROM date_range
            WHERE date < :final_date
        )
        SELECT 
            d.date,
            COALESCE(p.amount, :base_price) AS price
        FROM 
            date_range d
        LEFT JOIN seasons s
            ON d.date BETWEEN s.initial_date AND s.final_date
        LEFT JOIN prices p
            ON s.id = p.season_id AND p.room_id = :room_id
    ";

    // Run query
    $results = DB::select(query: $query, bindings: [
      'room_id' => $roomId,
      'initial_date' => $initialDate,
      'final_date' => $finalDate,
      'base_price' => $basePrice,
    ]);

    $total = 0.00;

    foreach ($results as $element) {
      $total += $element->price;
    }

    return number_format(num: $total, decimals: 2, decimal_separator: '', thousands_separator: '');
  }
}