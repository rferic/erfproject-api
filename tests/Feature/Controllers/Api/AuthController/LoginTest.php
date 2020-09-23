<?php

namespace Tests\Feature\Controllers\Api\AuthController;

use App\Models\LinkedSocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = route('api.auth.login');
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        $this
            ->get($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testFailEmptyType (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['type']);
    }

    /*** DEFAULT LOGIN ***/
    public function testFailEmptyParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
            'type' => 'default'
        ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
                'type' => 'default',
                'email' => $this->faker->word,
                'password' => $this->faker->password
            ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function testFailVWrongParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
                'type' => 'default',
                'email' => $this->faker->email,
                'password' => $this->faker->password
            ])
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
                'type' => 'default',
                'email' => config('default.admin.email'),
                'password' => config('default.admin.password'),
                'remember_me' => $this->faker->boolean
            ])
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'access_token',
                    'expires_at'
                ]
            ]);
    }

    /*** SOCIAL LOGIN ***/
    public function testSocialFailEmptyParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
            'type' => 'social'
        ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['provider', 'token']);
    }

    public function testSocialFailValidationParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
            'type' => 'social',
            'provider' => $this->faker->word,
            'token' => $this->faker->word
        ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['provider', 'token']);

        $this->postJson($this->route, [
            'type' => 'social',
            'provider' => 'github',
            'token' => $this->faker->word
        ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['token']);
    }

    public function testSocialSuccess (): void
    {
        $this->withExceptionHandling();

        $token = $this->faker->word;

        LinkedSocialAccount::create([
            'user_id' => User::all()->random()->id,
            'provider_id' => $this->faker->uuid,
            'provider_name' => 'github',
            'provider_data' => [],
            'token' => $token
        ]);

        $this->postJson($this->route, [
            'type' => 'social',
            'provider' => 'github',
            'token' => $token
        ])
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'access_token',
                    'expires_at'
                ]
            ]);
    }
}
