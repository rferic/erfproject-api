<?php

namespace Tests\Feature\Services\Auth;

use App\Http\Services\Auth\LoginAuthService;
use App\Http\Services\User\CreateUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginAuthServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user, $password;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->password = $this->faker->password;

        $createService = new CreateUserService([
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
            'password' => $this->password
        ]);
        $this->user = $createService->execute();
    }

    public function testFailAttempt (): void
    {
        $this->withExceptionHandling();

        $service = new LoginAuthService([
            'email' => $this->faker->safeEmail,
            'password' => $this->faker->word,
            'remember_me' => $this->faker->boolean
        ]);
        $response = $service->execute();
        self::assertNull($response);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        $service = new LoginAuthService([
            'email' => $this->user->email,
            'password' => $this->password,
            'remember_me' => $this->faker->boolean
        ]);
        $response = $service->execute();

        self::assertIsArray($response);
        self::assertArrayHasKey('access_token', $response);
        self::assertArrayHasKey('expires_at', $response);
        self::assertTrue(auth()->check());
        self::assertEquals(auth()->id(), $this->user->id);
    }
}
