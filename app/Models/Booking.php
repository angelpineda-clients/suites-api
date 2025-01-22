<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

  protected $guarded = [];

  public function room()
  {
    return $this->belongsTo(Room::class);
  }

  public function payment()
  {
    return $this->hasOne(Payment::class);
  }
}
