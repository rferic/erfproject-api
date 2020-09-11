<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

use App\Http\Resources\User\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $routeName, $route, $me;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->routeName = 'api.me.show';
        $this->route = route($this->routeName);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);
        $resource = (new UserResource($this->me))->jsonSerialize();;
        ksort($resource);

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data'
            ])
            ->assertJsonFragment([
                'data' => $resource
            ]);
    }
}
