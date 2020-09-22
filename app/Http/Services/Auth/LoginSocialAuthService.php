<?php


namespace App\Http\Services\Auth;


use App\LinkedSocialAccount;
use Carbon\Carbon;

class LoginSocialAuthService
{
    protected $provider;
    protected $token;

    public function __construct ( String $provider, String $token )
    {
        $this->provider =  $provider;
        $this->token =  $token;
    }

    public function execute (): array
    {
        $linkedSocialAccount = LinkedSocialAccount::where('token', $this->token)
            ->where('provider_name', $this->provider)
            ->first();

        auth()->loginUsingId($linkedSocialAccount->user->id, true);

        $linkedSocialAccount->token = null;
        $linkedSocialAccount->save();

        $tokenResult = auth()->user()->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return [
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ];
    }
}
