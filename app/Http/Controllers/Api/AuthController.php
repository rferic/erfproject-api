<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\User\UserBaseResource;
use App\Http\Services\Auth\LoginAuthService;
use App\Http\Services\User\CreateUserService;
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
        $service = new LoginAuthService($request->all());
        $response = $service->execute();

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

        return $this->responseSuccess(new UserBaseResource($user), 'Successfully logged out!', JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * Sign up a new account
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function signUp ( StoreRequest $request ): JsonResponse
    {
        $service = new CreateUserService($request->all());
        $user = $service->execute();

        return $this->responseSuccess(
            new UserBaseResource($user),
            'User has been created',
            JsonResponse::HTTP_CREATED
        );
    }
}
