<?php

namespace Tests\Feature\Controllers\Api\AuthController;

use App\Http\Resources\User\UserBaseResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = route('api.auth.singUp');
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        $this->get($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testFailEmptyParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'name', 'password']);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route, [
                'email' => User::all()->first()->email,
                'name' => null,
                'password' => 'test'
            ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'name', 'password']);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        $data = [
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
            'password' => $this->faker->password
        ];

        $response = $this->postJson($this->route, $data)
            ->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJsonStructure(['message', 'data']);

        $queryCreated = User::query();

        foreach ( $data as $key => $value ) {
            if ( $key !== 'password' ) {
                $queryCreated->where($key, $value);
            }
        }

        $userCreated = $queryCreated->first();

        $resource = (new UserBaseResource($userCreated))->jsonSerialize();
        ksort($resource);

        $response->assertJsonFragment([
            'data' => $resource
        ]);
    }
}
