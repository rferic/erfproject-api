<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CollectionRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\User\CreateUserService;
use App\Http\Services\User\DestroyUserService;
use App\Http\Services\User\GetCollectionUserAbstract;
use App\Http\Services\User\UpdateUserService;
use App\Http\Utils\ApiResponse;
use App\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index ( CollectionRequest $request ): JsonResponse
    {
        $getCollectionUserService = new GetCollectionUserAbstract($request->all());
        $users = $getCollectionUserService->execute();

        return $this->responseSuccess($users, 'Users retry', JsonResponse::HTTP_OK);
    }

    public function show ( User $user ): JsonResponse
    {
        return $this->responseSuccess(new UserResource($user), 'Retry user', JsonResponse::HTTP_OK);
    }

    public function store ( StoreRequest $request ): JsonResponse
    {
        $createUserService = new CreateUserService($request->all());
        $user = $createUserService->execute();

        return $this->responseSuccess($user, 'User has been created', JsonResponse::HTTP_OK);
    }

    public function update ( User $user, UpdateRequest $request ): JsonResponse
    {
        $updateUserService = new UpdateUserService($user, $request->all());
        $data = $updateUserService->execute();

        return $this->responseSuccess($data, 'User has been updated', JsonResponse::HTTP_OK);
    }

    public function destroy ( User $user )
    {
        $destroyUserService = new DestroyUserService($user);
        $destroyUserService->execute();

        return $this->responseSuccess($user, 'User has been destroyed', JsonResponse::HTTP_OK);
    }
}
