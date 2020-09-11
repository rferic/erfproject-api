<?php

namespace Tests\Feature\Controllers\Api\UserController;

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

    protected $routeName, $route, $me, $root;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->root = $this->getRoot();
        $this->routeName = 'api.users.show';
        $this->route = route($this->routeName, $this->me->id);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testFailEntityNotFound (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->getJson(route($this->routeName, 9999))
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);
        $resource = (new UserResource($this->me))->jsonSerialize();;
        ksort($resource);

        $this->getJson(route($this->routeName, $this->me->id))
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
