<?php

namespace App\Http\Controllers;


use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
  /**
   * Pagina los datos de una consulta y devuelve los datos y la paginación por separado.
   *
   * @param Builder $query La consulta del modelo para paginar.
   * @param int $perPage Número de elementos por página.
   * @param int $page Página actual.
   * @return JsonResponse
   */
  protected function paginateData(Builder $query, int $perPage = 10, int $page = 1): array
  {
    // Realiza la paginación en la consulta dada
    $paginator = $query->paginate(perPage: $perPage, columns: ['*'], pageName: 'page', page: $page);

    // Separa los datos de los elementos y la información de paginación
    $data = $paginator->items();
    $pagination = [
      'total' => $paginator->total(),
      'per_page' => $paginator->perPage(),
      'current_page' => $paginator->currentPage(),
      'last_page' => $paginator->lastPage(),
      'from' => $paginator->firstItem(),
      'to' => $paginator->lastItem()
    ];

    return [
      'data' => $data,
      'pagination' => $pagination
    ];
  }

  /**
   * Recorre un arreglo de imagenes y las elimina de la nube
   * @param array $images
   * @return void
   */
  protected function deleteImagesFromCloudinary($images)
  {
    foreach ($images as $image) {
      if (isset($image->public_id)) {
        Cloudinary::destroy(publicId: $image->public_id);
      }
    }
  }
}
