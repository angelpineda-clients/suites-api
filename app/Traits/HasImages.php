<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasImages
{
  /**
   * Get related images in a model.
   *
   * @param Model|null $model
   * @return \Illuminate\Support\Collection
   */
  public function getImagesFromModel(?Model $model)
  {
    if ($model && method_exists(object_or_class: $model, method: 'images')) {
      return $model->images->map(function ($img) {
        return [
          'id' => $img->id,
          'url' => $img->url,
          'public_id' => $img->public_id,
        ];
      });
    }

    return collect(); // return empty collection
  }
}