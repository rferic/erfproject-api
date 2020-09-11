<?php

namespace Tests\Feature\Controllers\Api\UserController;

use App\Http\Services\User\GetCollectionUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me, $root;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = 'api.users.index';
        $this->me = $this->getMe();
        $this->root = $this->getRoot();
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->getJson(route($this->route))
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->postJson(route($this->route))
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->postJson(route($this->route))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $response = $this->getJson(route($this->route, [
            'page' => $this->faker->word,
            'per_page' => $this->faker->word,
            'with' => $this->faker->word

        ]))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'page',
                    'per_page',
                    'with'
                ]
            ]);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->getJson(route($this->route))
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
            'page' => $this->faker->numberBetween(1, 2),
            'per_page' => $this->faker->numberBetween(1, 20),
            'filterText' => $this->faker->boolean ? 'a' : null,
            'filters' => [
                'role' => $this->faker->boolean ? $this->faker->randomElement(['root', 'client']) : null
            ],
            'with' => $this->faker->boolean ? $this->faker->shuffleArray(['applicant_relations', 'addressee_relations', 'relations']) : []
        ];

        $response = $this->getJson(route($this->route, $request))
            ->assertStatus(JsonResponse::HTTP_OK);

        $service = new GetCollectionUserService($request);

        $this->assertCollection($response, $request, $service);
    }
}
