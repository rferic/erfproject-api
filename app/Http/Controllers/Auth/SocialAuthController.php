<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\User\FindOrCreateSocialUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Handle social login
     * @param String $provider
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function __invoke ( String $provider, Request $request )
    {
        $frontends = array_keys(config('frontend'));
        $frontend = $request->frontend;

        if ( !in_array($frontend, $frontends, true) ) {
            throw new \RuntimeException('Not is a available frontend', JsonResponse::HTTP_NOT_FOUND);
        }

        $socialUser = Socialite::driver($provider)->stateless()->user();
        (new FindOrCreateSocialUserService($socialUser, $provider))->execute();
        $urlRedirect = config('frontend.' . $frontend . '.redirect_social_login') . '?p=' . $provider . '&t=' . $socialUser->token;

        return redirect()->to($urlRedirect);
    }
}
