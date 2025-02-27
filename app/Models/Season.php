<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends BaseModel
{
  use HasFactory;

  protected $fillable = ['name', 'alias', 'initial_date', 'final_date'];
  protected $guard = [];

  protected function name(): Attribute
  {
    return Attribute::make(
      get: fn(string $value): string => ucfirst(string: $value),
      set: fn(string $value): string => strtoupper(string: $value)
    );
  }

  protected function alias(): Attribute
  {
    return Attribute::make(
      set: fn(string|null $value): string => strtoupper(string: $value),
    );
  }

  public function price()
  {
    return $this->hasMany(Price::class);
  }
}
