<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use \App\Http\Requests\SocialAuth\LoginRequest as SocialLoginRequest;
use App\Http\Resources\User\UserBaseResource;
use App\Http\Services\Auth\LoginAuthService;
use App\Http\Services\Auth\LoginSocialAuthService;
use App\Http\Services\User\CreateUserService;
use App\Http\Utils\ApiResponse;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

/**
 * @property array frontends
 */
class AuthController extends Controller
{
    use ApiResponse;

    private $socialLogins = ['github'];

    /**
     * Login
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login ( LoginRequest $request ): JsonResponse
    {
        switch ( $request->type ) {
            case 'default':
                $service = new LoginAuthService($request->all());
                break;
            case 'social':
                $service = new LoginSocialAuthService($request->provider, $request->token);
                break;
            default:
                return $this->responseFail([], 'Login type not available', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->loginWithService($service);
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

    /**
     * Redirect to social auth
     * @param String $provider
     * @param String $frontend
     * @return JsonResponse
     */
    public function redirectToSocialAuth ( String $provider, String $frontend ): JsonResponse
    {
        $frontends = array_keys(config('frontend'));

        if ( !in_array($provider, $this->socialLogins, true) ) {
            return $this->responseFail(null, 'Not is a available social login', JsonResponse::HTTP_BAD_REQUEST);
        }

        if ( !in_array($frontend, $frontends, true) ) {
            return $this->responseFail(null, 'Not is a available frontend', JsonResponse::HTTP_BAD_REQUEST);
        }

        $socialite = Socialite::driver($provider)->stateless()
            ->redirect()
            ->getTargetUrl();

        return $this->responseSuccess($socialite, 'Redirect to social login');
    }

    /**
     * Unification process login
     * @param $service
     * @return JsonResponse
     */
    private function loginWithService ( $service ): JsonResponse
    {
        $response = $service->execute();

        if ( !$response ) {
            return $this->responseFail(null, 'Unauthorized', JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->responseSuccess($response, 'You are logged!', JsonResponse::HTTP_ACCEPTED);
    }
}
