<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
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
            'medias' => $medias->toResourceCollection(),
        ]);
    }

    public function create(Request $request)
    {
        $validator = MediaValidators::create($request->all());
        $medias = $this->services->create(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'medias' => $medias->toResourceCollection(),
        ]);
    }

    public function delete(Request $request)
    {
        $validator = MediaValidators::find($request->all());

        $medias = $this->services->allByObject(getModel());
        if ($medias->count() === 1) {
            return Error('last Media can not be deleted');
        }
        $media = $medias->find($validator->safe()->integer('media_id'));
        $this->services->delete($media);

        return Success(msg: 'media deleted');
    }
}
