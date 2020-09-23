<?php

namespace Tests\Feature\Services\Auth;

use App\Http\Services\Auth\LoginSocialAuthService;
use App\Models\LinkedSocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginSocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user, $provider, $token;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->user = User::all()->random();
        $this->provider = $this->faker->randomElement(['github']);
        $this->token = $this->faker->word;
    }

    public function testFailAttempt (): void
    {
        $this->withExceptionHandling();

        $service = new LoginSocialAuthService($this->faker->word, $this->faker->word);
        $response = $service->execute();
        self::assertNull($response);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        LinkedSocialAccount::create([
            'user_id' => $this->user->id,
            'provider_id' => $this->faker->uuid,
            'provider_name' => $this->provider,
            'provider_data' => [],
            'token' => $this->token
        ]);

        $service = new LoginSocialAuthService($this->provider, $this->token);
        $response = $service->execute();

        self::assertIsArray($response);
        self::assertArrayHasKey('access_token', $response);
        self::assertArrayHasKey('expires_at', $response);
        self::assertTrue(auth()->check());
        self::assertEquals(auth()->id(), $this->user->id);
    }
}
