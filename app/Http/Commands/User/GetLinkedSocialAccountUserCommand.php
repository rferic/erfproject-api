<?php


namespace App\Http\Commands\User;


use Laravel\Socialite\Two\User as ProviderUser;

class GetLinkedSocialAccountUserCommand
{
    protected $providerUser;
    protected $provider;

    public function __construct ( ProviderUser $providerUser, string $provider )
    {
        $this->providerUser = $providerUser;
        $this->provider = $provider;
    }

    public function execute ()
    {
        return \App\Models\LinkedSocialAccount::where('provider_name', $this->provider)
            ->where('provider_id', $this->providerUser->getId())
            ->first();
    }
}
