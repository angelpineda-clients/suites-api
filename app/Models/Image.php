<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{


  public function imageable()
  {
    return $this->morphedByMany('App\Models\Imageable', 'imageable');
  }
}
