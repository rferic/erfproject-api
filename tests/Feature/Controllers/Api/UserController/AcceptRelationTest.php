<?php

namespace Tests\Feature\Controllers\Api\UserController;

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

    protected $route, $root, $applicant, $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();
        $this->route = 'api.users.relations.accept';
        $this->root = $this->getRoot();
        $this->applicant = User::where('email', '<>', $this->root->email)->first();
        $this->addressee = User::where('email', '<>', $this->root->email)->where('email', '<>', $this->applicant->email)->first();
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->applicant);

        $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailValidationParams (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
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
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);

        Passport::actingAs($this->root);

        $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
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
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        Passport::actingAs($this->root);

        $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
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
            'addressee_id' => $this->applicant->id,
            'status' => 'pending'
        ]);

        Passport::actingAs($this->root);

        $response = $this->putJson(route($this->route, [
            'user' => $this->applicant->id,
            'addressee' => $this->addressee->id
        ]))
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->addressee->id)->where('addressee_id', $this->applicant->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }

    public function testSuccessIsBlockedByAddressee (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        Passport::actingAs($this->root);

        $response = $this->putJson(route($this->route, [
            'user' => $this->addressee->id,
            'addressee' => $this->applicant->id
        ]))
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        $relation = Relation::where('applicant_id', $this->applicant->id)->where('addressee_id', $this->addressee->id)->first();
        $resource = (new RelationResource($relation))->jsonSerialize();
        $this->assertEntity($response['data'], $resource);
    }
}
