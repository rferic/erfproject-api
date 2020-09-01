<?php


namespace App\Http\Services\Auth;


use Carbon\Carbon;

class LoginAuthService
{
    private $request;

    public function __construct ( $request )
    {
        $this->request = $request;
    }

    public function execute (): ?array
    {
        $credentials = [
            'email' => $this->request->email,
            'password' => $this->request->password
        ];

        if ( !auth()->attempt($credentials) ) {
            return null;
        }

        $tokenResult = auth()->user()->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ( $this->request->remember_me ) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return [
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ];
    }
}
