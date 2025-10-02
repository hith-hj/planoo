<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Services\MediaServices;
use App\Validators\MediaValidators;
use Illuminate\Http\Request;

final class MediaController extends Controller
{
    public function __construct(public MediaServices $services) {}

    public function all()
    {
        $medias = $this->services->allByObject(getModel());

        return Success(payload: [
            'medias' => MediaResource::collection($medias),
        ]);
    }

    public function create(Request $request)
    {
        $validator = MediaValidators::create($request->all());
        $media = $this->services->create(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'media' => MediaResource::collection($media->fresh()),
        ]);
    }

    public function delete(Request $request)
    {
        $validator = MediaValidators::find($request->all());
        $media = $this->services->findByObject(
            getModel(),
            $validator->safe()->integer('media_id')
        );
        $this->services->delete($media);

        return Success(msg: 'media deleted');
    }
}
