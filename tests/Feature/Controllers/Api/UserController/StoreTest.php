<?php

namespace Tests\Feature\Controllers\Api\UserController;

use App\Http\Resources\User\UserBaseResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreTest extends TestCase
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
        $this->route = route('api.users.store');
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->postJson($this->route, [
            'email' => $this->me->email,
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
