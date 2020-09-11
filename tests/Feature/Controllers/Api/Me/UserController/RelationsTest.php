<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

use App\Http\Services\User\GetRelationsCollectionUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $routeName, $route, $me;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->routeName = 'api.me.relations';
        $this->route = route($this->routeName);
        $this->me = $this->getMe();
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailRequestParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->getJson(route($this->routeName, [
            'status' => $this->faker->word
        ]))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => ['status']
            ]);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->getJson($this->route)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'collection',
                    'pagination'
                ]
            ]);
    }

    public function testSuccessWithFilter (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $request = [
            'user' => $this->me->id,
            'page' => $this->faker->numberBetween(1, 2),
            'per_page' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->boolean ? $this->faker->randomElement(['pending', 'friendship', 'hate']) : null
        ];

        $response = $this->getJson(route($this->route, $request))
            ->assertStatus(JsonResponse::HTTP_OK);

        $service = new GetRelationsCollectionUserService($this->me, $request);

        $this->assertCollection($response, $request, $service);
    }
}
