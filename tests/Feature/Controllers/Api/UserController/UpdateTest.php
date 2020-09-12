<?php

namespace Tests\Feature\Controllers\Api\UserController;

use App\Http\Resources\User\UserResource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me, $root;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->root = $this->getRoot();
        $this->route = route('api.users.update', $this->me);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->putJson($this->route, [
            'email' => $this->root->email,
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
            'password' => 'test123!'
        ];

        Passport::actingAs($this->root);

        $response = $this->putJson($this->route, $data)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $queryCreated = User::query();

        foreach ( $data as $key => $value ) {
            if ( $key !== 'password' ) {
                $queryCreated->where($key, $value);
            }
        }

        $userUpdated = $queryCreated->first();

        $resource = (new UserResource($userUpdated))->jsonSerialize();
        ksort($resource);

        $response->assertJsonFragment(['data' => $resource]);
    }
}
