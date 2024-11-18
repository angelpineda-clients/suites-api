<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Image;
use App\Models\Room;
use App\Traits\HasImages;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{

  use HasImages;

  public function store()
  {

  }

  public function index(Request $request)
  {

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {
      $query = Image::query();

      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return response()->json(data: [
        $data
      ], status: Response::HTTP_OK);

    } catch (\Exception $e) {

      return response()->json(data: [
        'error' => 'Not expected error (image)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show()
  {

  }

  public function update()
  {

  }

  public function delete(Request $request, string $id)
  {

    $type = $request['type'];
    $model_id = $request['model_id'];

    try {
      $image = Image::findOrFail(id: $id);
      $public_id = $image->public_id;

      // delete: resources
      $wasDeleted = Cloudinary::destroy(publicId: $public_id);

      if ($wasDeleted['result'] == 'ok') {
        $image->delete();
      }

      // data: get model
      $result = match ($type) {
        'room' => Room::find(id: $model_id),
        default => null
      };

      // data: build data to return
      $images = $this->getImagesFromModel(model: $result);


      return ApiResponse::success(data: $images, message: "Images for $type");

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (image)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (image)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}