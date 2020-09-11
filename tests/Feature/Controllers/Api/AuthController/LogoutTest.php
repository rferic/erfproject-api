<?php

namespace Tests\Feature\Controllers\Api\AuthController;

use App\Http\Resources\User\UserBaseResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = route('api.auth.logout');
        $this->me = $this->getMe();
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();
        $resource = (new UserBaseResource($this->me))->jsonSerialize();
        ksort($resource);

        Passport::actingAs($this->me);

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_ACCEPTED)
            ->assertJsonStructure(['message', 'data'])
            ->assertJsonFragment([
                'data' => $resource
            ]);
    }
}
