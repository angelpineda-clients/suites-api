<?php

namespace App\Services;

use App\Models\Room;

class RoomService
{

  /**
   * searchRoomAvailability
   * search rooms availables
   * @param number $people
   * @param \date $checkIn
   * @param \date $checkOut
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function searchRoomAvailability($people, $checkIn, $checkOut)
  {
    return Room::query()
      ->where('capacity', '>=', $people)
      ->where('active', true)
      ->whereDoesntHave(relation: 'booking', callback: function ($query) use ($checkIn, $checkOut) {
        $query->where(function ($q) use ($checkIn, $checkOut) {
          $q->where('check_in', '<=', $checkOut)
            ->where('check_out', '>=', $checkIn);
        })->whereNot(function ($query) use ($checkIn, $checkOut) {
          $query->where('check_out', '=', $checkIn)
            ->orWhere('check_in', '=', $checkOut);
        });
      });
  }
}