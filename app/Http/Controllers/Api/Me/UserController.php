<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Http\Requests\Relation\CollectionRequest as RelationCollectionRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Relation\RelationResource;
use App\Http\Resources\User\UserResource;
use App\Http\Services\User\DestroyRelationUserService;
use App\Http\Services\User\DestroyUserService;
use App\Http\Services\User\GetRelationsCollectionUserService;
use App\Http\Services\User\RequestRelationUserService;
use App\Http\Services\User\UpdateRelationUserService;
use App\Http\Services\User\UpdateUserService;
use App\Http\Utils\ApiResponse;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;

    public function show (): JsonResponse
    {
        return $this->responseSuccess(new UserResource(Auth::user()), 'Retry user', JsonResponse::HTTP_OK);
    }

    public function update ( UpdateRequest $request ): JsonResponse
    {
        $service = new UpdateUserService(Auth::user(), $request->all());
        $user = $service->execute();

        return $this->responseSuccess(new UserResource($user), 'User has been updated', JsonResponse::HTTP_OK);
    }

    public function destroy (): JsonResponse
    {
        $service = new DestroyUserService(Auth::user());
        $user = $service->execute();

        return $this->responseSuccess(new UserResource($user), 'User has been destroyed', JsonResponse::HTTP_OK);
    }

    // Relations
    public function relations ( RelationCollectionRequest $request ): JsonResponse
    {
        $service = new GetRelationsCollectionUserService(Auth::user(), $request->all());
        return $this->responseSuccess($service->execute(), 'Retry relations user', JsonResponse::HTTP_OK);
    }

    public function requestRelation ( User $addressee ): JsonResponse
    {
        $service = new RequestRelationUserService(Auth::user(), $addressee);
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been created', JsonResponse::HTTP_CREATED);
    }

    public function acceptRelation ( User $addressee ): JsonResponse
    {
        $service = new UpdateRelationUserService(Auth::user(), $addressee, 'friendship');
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been accepted', JsonResponse::HTTP_OK);
    }

    public function blockRelation ( User $addressee ): JsonResponse
    {
        $service = new UpdateRelationUserService(Auth::user(), $addressee, 'hate');
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been blocked', JsonResponse::HTTP_OK);
    }

    public function destroyRelation ( User $addressee ): JsonResponse
    {
        $service = new DestroyRelationUserService(Auth::user(), $addressee);
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been destroyed', JsonResponse::HTTP_OK);
    }
}
