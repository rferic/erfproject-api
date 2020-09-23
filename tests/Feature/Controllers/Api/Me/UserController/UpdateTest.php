<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

use App\Http\Resources\User\UserBaseResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->route = route('api.me.update');
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->putJson($this->route, [
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

        Passport::actingAs($this->me);

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

        $resource = (new UserBaseResource($userUpdated))->jsonSerialize();
        ksort($resource);

        $response->assertJsonFragment([
            'data' => $resource
        ]);
    }
}
