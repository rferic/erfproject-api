<?php

namespace App\Http\Middleware;

use App\Http\Utils\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    use ApiResponse;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function redirectTo ( $request )
    {
        if ( !$request->expectsJson() ) {
            return route('login');
        }
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param Request $request
     * @param  array  $guards
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function unauthenticated ( $request, Array $guards )
    {
        if ( $request->expectsJson() ) {
            throw new HttpResponseException(
                $this->responseFail(null, 'Unauthenticated', JsonResponse::HTTP_UNAUTHORIZED)
            );
        }

        parent::unauthenticated($request, $guards);
    }
}
