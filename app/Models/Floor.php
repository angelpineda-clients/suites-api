<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Floor extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'alias'];
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
            set: fn(string $value): string => strtoupper(string: $value),
        );
    }

    public function room()
    {
        return $this->hasMany(Room::class);
    }
}
