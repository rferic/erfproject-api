<?php

namespace Tests\Feature\Controllers\Api\UserController;

use App\Http\Resources\User\UserBaseResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DestroyTest extends TestCase
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
        $this->route = route('api.users.destroy', $this->me);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->deleteJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->deleteJson($this->route)
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $userDestroyed = $this->me;
        $response = $this->deleteJson($this->route)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $resource = (new UserBaseResource($userDestroyed))->jsonSerialize();
        ksort($resource);

        $response->assertJsonFragment([
            'data' => $resource
        ]);

        self::assertFalse(User::where('email', $userDestroyed->email)->exists());
    }
}
