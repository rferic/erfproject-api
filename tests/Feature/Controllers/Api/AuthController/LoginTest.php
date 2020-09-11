<?php

namespace Tests\Feature\Controllers\Api\AuthController;

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

    public function testFailEmptyParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function testFailValidationParams (): void
    {

        $this->postJson($this->route, [
                'email' => $this->faker->word,
                'password' => $this->faker->password
            ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function testFailVWrongParams (): void
    {

        $this->postJson($this->route, [
                'email' => $this->faker->email,
                'password' => $this->faker->password
            ])
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
                'email' => config('default.admin.email'),
                'password' => config('default.admin.password')
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
