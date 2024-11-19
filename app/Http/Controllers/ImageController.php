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
use Validator;

class ImageController extends Controller
{

  use HasImages;

  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'model_type' => 'required|string',
      'model_id' => 'required|string'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $model_type = $request->input(key: 'model_type');
    $model_id = $request->input(key: 'model_id');
    $file = $request->file(key: 'image');


    try {

      $entity = match ($model_type) {
        'room' => Room::find(id: $model_id),
        default => null,
      };

      if ($entity == null) {
        return ApiResponse::error(message: 'Model not found', errors: ['message' => "Not results for $model_id in model $model_type"]);
      }

      $cloudinary_result = Cloudinary::upload(file: $file->getRealPath());

      if ($cloudinary_result) {
        $image = new Image();
        $image->url = $cloudinary_result->getSecurePath();
        $image->public_id = $cloudinary_result->getPublicId();
        $image->save();

        $entity->images()->attach($image->id);
      }

      $entity = match ($model_type) {
        'room' => Room::find(id: $model_id)->with(['images'])->first(),
        default => null,
      };

      $images = $entity->images;

      return ApiResponse::success(data: $images);
    } catch (\Exception $e) {

      ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  public function index(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'model_type' => 'nullable|string',
      'model_id' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $model_type = $request->input(key: 'model_type');
    $model_id = $request->input(key: 'model_id');

    try {

      if (!$model_type || !$model_id) {
        return ApiResponse::success(data: []);
      }

      $entity = match ($model_type) {
        'room' => Room::find(id: $model_id)->with(['images'])->first(),
        default => null,
      };

      $images = $entity->images;

      return ApiResponse::success(data: $images);

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error (image)', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }

  public function show(string $id)
  {
    try {
      $image = Image::findOrFail(id: $id);

      return ApiResponse::success(data: $image);
    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found (image)', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error (image)', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'model_type' => 'required|string',
      'model_id' => 'required|string'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $model_type = $request->input(key: 'model_type');
    $model_id = $request->input(key: 'model_id');
    $file = $request->file(key: 'image');


    try {
      $image = Image::findOrFail(id: $id);

      $cloudinary_result = Cloudinary::upload(file: $file->getRealPath());

      if (!$cloudinary_result) {
        return ApiResponse::error(message: 'Error to update image', errors: ['message' => 'Can not upload image to cloudinary']);
      }

      $wasDeleted = Cloudinary::destroy(publicId: $image->public_id);

      if ($wasDeleted['result'] !== 'ok') {

        Cloudinary::destroy(publicId: $cloudinary_result->getPublicId());

        return ApiResponse::error(message: 'Can not delete image', errors: ['message' => 'Error trying to delete image when update']);
      }

      $image->url = $cloudinary_result->getSecurePath();
      $image->public_id = $cloudinary_result->getPublicId();
      $image->save();

      if (!$model_type || !$model_id) {
        return ApiResponse::success(data: $image, );
      }

      $entity = match ($model_type) {
        'room' => Room::find(id: $model_id)->with(['images'])->first(),
        default => null,
      };

      $images = $entity->images;

      return ApiResponse::success(data: $images);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found (image)', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error (image)', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'model_type' => 'nullable|string',
      'model_id' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $model_type = $request->input(key: 'model_type');
    $model_id = $request->input(key: 'model_id');

    try {
      $image = Image::findOrFail(id: $id);
      $public_id = $image->public_id;

      // delete: resources
      $wasDeleted = Cloudinary::destroy(publicId: $public_id);

      if ($wasDeleted['result'] == 'ok') {
        $image->delete();
      }

      // data: build data to return
      if (!$model_type || !$model_id) {
        return ApiResponse::success(data: $image, );
      }

      $entity = match ($model_type) {
        'room' => Room::find(id: $model_id)->with(['images'])->first(),
        default => null,
      };

      $images = $entity->images;


      return ApiResponse::success(data: $images);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found (image)', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error (image)', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}