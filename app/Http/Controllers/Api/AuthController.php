<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Auth\LoginAuthService;
use App\Http\Services\User\CreateUserService;
use App\Http\Services\User\DestroyUserService;
use App\Http\Services\User\UpdateUserService;
use App\Http\Utils\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Login
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login ( LoginRequest $request ): JsonResponse
    {
        $loginAuthService = new LoginAuthService($request);
        $response = $loginAuthService->execute();

        if ( !$response ) {
            return $this->responseFail(null, 'Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->responseSuccess($response, 'You are logged!', JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * Logout
     * @return JsonResponse
     */
    public function logout (): JsonResponse
    {
        $user = auth()->user();
        request()->user()->token()->revoke();

        return $this->responseSuccess(new UserResource($user), 'Successfully logged out!', JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * Sign up a new account
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function signUp ( StoreRequest $request ): JsonResponse
    {
        $createUserService = new CreateUserService($request->all());
        $user = $createUserService->execute();

        return $this->responseSuccess(
            new UserResource($user),
            'User has been created',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Get your user
     * @return JsonResponse
     */
    public function user (): JsonResponse
    {
        return $this->responseSuccess(new UserResource(auth()->user()), 'Retry your user', JsonResponse::HTTP_OK);
    }

    /**
     * Update your user
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update ( UpdateRequest $request ): JsonResponse
    {
        $updateUserService = new UpdateUserService(auth()->user(), $request->all());
        $user = $updateUserService->execute();

        return $this->responseSuccess(
            new UserResource($user),
            'User has been updated',
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Destroy your user
     * @return JsonResponse
     */
    public function destroy (): JsonResponse
    {
        $user = auth()->user();
        $destroyUserService = new DestroyUserService($user);
        $destroyUserService->execute();

        return $this->responseSuccess(
            new UserResource($user),
            'User has been destroyed',
            JsonResponse::HTTP_OK
        );
    }
}
