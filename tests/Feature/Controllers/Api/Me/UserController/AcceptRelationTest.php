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

class AcceptRelationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $route, $me, $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = 'api.me.relations.accept';
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

    public function testFailIsBlocked (): void
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

    public function testSuccessIsPendingToAddressee (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->addressee->id,
            'addressee_id' => $this->me->id,
            'status' => 'pending'
        ]);

        Passport::actingAs($this->me);

        $response = $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->addressee->id)->where('addressee_id', $this->me->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }

    public function testSuccessIsBlockedByAddressee (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->addressee->id,
            'addressee_id' => $this->me->id,
            'status' => 'hate',
            'blocker_id' => $this->me->id
        ]);

        Passport::actingAs($this->me);

        $response = $this->putJson(route($this->route, [
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->addressee->id)->where('addressee_id', $this->me->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }
}
