<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\MediaUploadingRequest;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait MediaUploadingTrait
{
    public function storeMedia(MediaUploadingRequest $request)
    {
        try {
            $media = DB::transaction(function () use ($request) {
                $user = $request->user();
                $class = '\\App\\Models\\' . $request->input('model_type');
                $collection = $request->input('collection_name', strtolower($request->input('model_type')) . '_attachments');

                if (in_array($request->input('model_type'), ['Customer', 'Lead'])) {
                    $this->checkUserPermission($request, $collection);
                }

                $model = new $class();
                $model->id = $request->input('model_id', 0);
                $model->exists = true;
                $media = $model->addMediaFromRequest('file')
                    ->withCustomProperties(['user_id' => $user->id]) // For logging purposes
                    ->toMediaCollection($collection);
                $media->wasRecentlyCreated = true;

                $media->updateQuietly(['user_id' => $user->id]);

                return $media;
            });

            return response()->json(compact('media'), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function checkUserPermission(Request $request, string $collection): void
    {
        $collection = Media::whereModelId($request->input('model_id', 0))
            ->whereModelType('App\\Models\\' . $request->input('model_type'))
            ->where('collection_name', $collection)
            ->whereFileName('.thumb')
            ->firstOrFail();

        abort_if(!$request->user()->canEditMedia($collection), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }
}
