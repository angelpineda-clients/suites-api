<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

  public function imageable()
  {
    return $this->morphedByMany(related: 'App\Models\Imageable', name: 'imageable');
  }
}
