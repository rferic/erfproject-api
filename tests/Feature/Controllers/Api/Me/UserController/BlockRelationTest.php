<?php

namespace Tests\Feature\Controllers\Api\Me\UserController;

use App\Http\Resources\Relation\RelationResource;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BlockRelationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me, $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = 'api.me.relations.block';
        $this->me = $this->getMe();
        $this->addressee = User::where('email', '<>', $this->me->email)->first();
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['addressee']);
    }

    public function testFailRequestAlreadyBlocked (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->me->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        Passport::actingAs($this->me);

        $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['addressee']);
    }

    public function testFailRequestIsPending (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->me->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);

        Passport::actingAs($this->me);

        $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['addressee']);
    }

    public function testSuccessIsBlockedByAddressee (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->me->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'friendship',
        ]);

        Passport::actingAs($this->me);

        $response = $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->me->id)->where('addressee_id', $this->addressee->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }
}
