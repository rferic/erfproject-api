<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

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

    protected $route, $me;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->route = route('api.me.destroy', $this->me);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->deleteJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $response = $this->deleteJson($this->route)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $resource = (new UserBaseResource($this->me))->jsonSerialize();
        ksort($resource);

        $response->assertJsonFragment([
            'data' => $resource
        ]);

        self::assertFalse(User::where('email', $this->me->email)->exists());
    }
}
