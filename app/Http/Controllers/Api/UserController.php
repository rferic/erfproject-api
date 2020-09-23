<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CollectionRequest;
use App\Http\Requests\Relation\CollectionRequest as RelationCollectionRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Relation\RelationResource;
use App\Http\Resources\User\UserResource;
use App\Http\Services\User\CreateUserService;
use App\Http\Services\User\DestroyRelationUserService;
use App\Http\Services\User\DestroyUserService;
use App\Http\Services\User\GetCollectionUserService;
use App\Http\Services\User\GetRelationsCollectionUserService;
use App\Http\Services\User\RequestRelationUserService;
use App\Http\Services\User\UpdateRelationUserService;
use App\Http\Services\User\UpdateUserService;
use App\Http\Utils\ApiResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;

    public function index ( CollectionRequest $request ): JsonResponse
    {
        $service = new GetCollectionUserService($request->all());
        $users = $service->execute();

        return $this->responseSuccess($users, 'Users retry', JsonResponse::HTTP_OK);
    }

    public function show ( User $user ): JsonResponse
    {
        return $this->responseSuccess(new UserResource($user), 'Retry user', JsonResponse::HTTP_OK);
    }

    public function store ( StoreRequest $request ): JsonResponse
    {
        $service = new CreateUserService($request->all());
        $user = $service->execute();

        return $this->responseSuccess(new UserResource($user), 'User has been created', JsonResponse::HTTP_CREATED);
    }

    public function update ( User $user, UpdateRequest $request ): JsonResponse
    {
        $service = new UpdateUserService($user, $request->all());
        $user = $service->execute();

        return $this->responseSuccess(new UserResource($user), 'User has been updated', JsonResponse::HTTP_OK);
    }

    public function destroy ( User $user ): JsonResponse
    {
        $service = new DestroyUserService($user);
        $service->execute();

        return $this->responseSuccess(new UserResource($user), 'User has been destroyed', JsonResponse::HTTP_OK);
    }

    // Roles
    public function attachRole ( User $user, String $roleName ): JsonResponse
    {
        $role = Role::where('name', $roleName)->first();

        if ( $user->id === Auth::id() ) {
            return $this->responseFail(null, 'You can not change your roles', JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ( !$role ) {
            return $this->responseFail(null, 'Role not found', JsonResponse::HTTP_NOT_FOUND);
        }

        if ( $user->hasRole($roleName) ) {
            return $this->responseFail(null, 'Role already attached', JsonResponse::HTTP_ALREADY_REPORTED);
        }


        $user->attachRole($role);

        return $this->responseSuccess(new UserResource($user), 'User has been attached role', JsonResponse::HTTP_OK);
    }

    public function detachRole ( User $user, String $roleName ): JsonResponse
    {
        $role = Role::where('name', $roleName)->first();

        if ( $user->id === Auth::id() ) {
            return $this->responseFail(null, 'You can not change your roles', JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ( !$role ) {
            return $this->responseFail(null, 'Role not found', JsonResponse::HTTP_NOT_FOUND);
        }

        if ( !$user->hasRole($roleName) ) {
            return $this->responseFail(null, 'Role is not attached', JsonResponse::HTTP_ALREADY_REPORTED);
        }


        $user->detachRole($role);

        return $this->responseSuccess(new UserResource($user), 'User has been detached role', JsonResponse::HTTP_OK);
    }

    // Relations
    public function relations ( User $user, RelationCollectionRequest $request ): JsonResponse
    {
        $service = new GetRelationsCollectionUserService($user, $request->all());
        return $this->responseSuccess($service->execute(), 'Retry relations user', JsonResponse::HTTP_OK);
    }

    public function requestRelation ( User $user, User $addressee ): JsonResponse
    {
        $service = new RequestRelationUserService($user, $addressee);
        $relation = $service->execute();

        return $this->responseSuccess(new RelationResource($relation), 'Relation has been created', JsonResponse::HTTP_CREATED);
    }

    public function acceptRelation ( User $user, User $addressee ): JsonResponse
    {
        $service = new UpdateRelationUserService($user, $addressee, 'friendship');
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been accepted', JsonResponse::HTTP_OK);
    }

    public function blockRelation ( User $user, User $addressee ): JsonResponse
    {
        $service = new UpdateRelationUserService($user, $addressee, 'hate');
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been blocked', JsonResponse::HTTP_OK);
    }

    public function destroyRelation ( User $user, User $addressee ): JsonResponse
    {
        $service = new DestroyRelationUserService($user, $addressee);
        $relation = $service->execute();
        return $this->responseSuccess(new RelationResource($relation), 'Relation has been destroyed', JsonResponse::HTTP_OK);
    }
}
