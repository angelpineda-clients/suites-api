<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Str;

class Room extends BaseModel
{
  use HasFactory;

  protected $guarded = [];

  public static function boot(): void
  {
    parent::boot();

    static::creating(callback: function ($room) {
      $room->slug = Str::slug($room->name);
    });

    static::updating(callback: function ($room) {
      $room->slug = Str::slug($room->name);
    });
  }

  public function size()
  {
    return $this->belongsTo(Size::class);
  }

  public function floor()
  {
    return $this->belongsTo(Floor::class);
  }

  public function services()
  {
    return $this->belongsToMany(Service::class, 'room_service');
  }

  public function images()
  {
    return $this->morphToMany('App\Models\Image', 'imageable');
  }
}
