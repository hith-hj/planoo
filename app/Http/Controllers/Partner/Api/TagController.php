<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Services\TagServices;
use App\Validators\TagValidators;
use Illuminate\Http\Request;

final class TagController extends Controller
{
    public function __construct(public TagServices $services) {}

    public function all()
    {
        $tags = $this->services->allByObject(getModel());

        return Success(payload: [
            'tags' => TagResource::collection($tags),
        ]);
    }

    public function create(Request $request)
    {
        $validator = TagValidators::create($request->all());
        $this->services->create(
            getModel(),
            $validator->safe()->all()
        );

        return Success('tag assigend');
    }

    public function delete(Request $request)
    {
        $validator = TagValidators::delete($request->all());
        $this->services->delete(
            getModel(),
            $validator->safe()->all()
        );

        return Success('tag removed');
    }
}
