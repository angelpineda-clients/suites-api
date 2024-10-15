<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $guarded = [];


    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value): string => ucfirst(string: $value),
            set: fn(string $value): string => strtoupper(string: $value)
        );
    }
}
