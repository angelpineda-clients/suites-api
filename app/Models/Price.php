<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
  use HasFactory;

  protected $guarded = [];

  public static function boot()
  {
    parent::boot();

    static::saving(function ($price) {

      // if another price is mark as default, changes the previous default price to false.
      if ($price->is_default) {
        static::where('room_id', $price->room_id)
          ->where('id', '!=', $price->id)
          ->update(['is_default' => false]);
      }
    });
  }

  public function room()
  {
    return $this->belongsTo(Room::class);
  }

  public function season()
  {
    return $this->belongsTo(Season::class);
  }
}
