<?php


namespace App\Http\Services\User;


use App\Http\Commands\User\GetLinkedSocialAccountUserCommand;
use App\User;
use Laravel\Socialite\Two\User as ProviderUser;

class FindOrCreateSocialUserService
{
    protected $providerUser;
    protected $provider;

    public function __construct ( ProviderUser $providerUser, string $provider )
    {
        $this->providerUser = $providerUser;
        $this->provider = $provider;
    }

    public function execute (): ?User
    {
        $user = null;
        $linkedAccountsUser = (new GetLinkedSocialAccountUserCommand($this->providerUser, $this->provider))->execute();

        if ( $linkedAccountsUser ) {
            $linkedAccountsUser->token = $this->providerUser->token;
            $linkedAccountsUser->save();
            return $linkedAccountsUser->user;
        }

        if ( $this->providerUser->getEmail() ) {
            $user = User::where('email', $this->providerUser->getEmail())->first();
        }

        if ( !$user ) {
            $user = (new CreateUserService([
                'name' => $this->providerUser->getName() ?? $this->providerUser->getNickname(),
                'email' => $this->providerUser->getEmail(),
                'password' => null
            ]))->execute();
        }

        $user->linkedSocialAccounts()->create([
            'provider_id' => $this->providerUser->getId(),
            'provider_name' => $this->provider,
            'provider_data' => $this->providerUser->getRaw(),
            'token' => $this->providerUser->token
        ]);

        return $user;
    }
}
