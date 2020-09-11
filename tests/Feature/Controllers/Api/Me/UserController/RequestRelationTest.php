<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

use App\Http\Resources\Relation\RelationResource;
use App\Relation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RequestRelationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me, $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->me = $this->getMe();
        $this->addressee = User::where('email', '<>', $this->me->email)->first();
        $this->route = route('api.me.relations.request', $this->addressee->id);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAlreadyExists (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->me->id,
            'addressee_id' => $this->addressee->id
        ]);

        Passport::actingAs($this->me);

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['addressee']);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $response = $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->me->id)->where('addressee_id', $this->addressee->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }
}
