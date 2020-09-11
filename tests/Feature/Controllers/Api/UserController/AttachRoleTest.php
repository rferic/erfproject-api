<?php

namespace Tests\Feature\Controllers\Api\UserController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AttachRoleTest extends TestCase
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
        $this->route = route('api.users.role.attach', [
            'user' => $this->me->id,
            'roleName' => 'root'
        ]);
    }

    public function testFailUnauthenticated (): void
    {
        $this->withExceptionHandling();

        $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure();
    }

    public function testFailAccessRole (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->me);

        $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_FORBIDDEN);
    }

    public function testFailMethod (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->postJson($this->route)
            ->assertStatus(JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testFailSelfChange (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->putJson(route('api.users.role.attach', [
            'user' => $this->root->id,
            'roleName' => 'client'
        ]))
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testFailEntityNotFound (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->putJson(route('api.users.role.attach', [
            'user' => $this->me->id,
            'roleName' => 'test'
        ]))
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND);
    }

    public function testFailAlreadyAttached (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $this->putJson(route('api.users.role.attach', [
            'user' => $this->me->id,
            'roleName' => 'client'
        ]))
            ->assertStatus(JsonResponse::HTTP_ALREADY_REPORTED);
    }

    public function testSuccess (): void
    {
        $this->withExceptionHandling();

        Passport::actingAs($this->root);

        $response = $this->putJson($this->route)
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(['message', 'data']);

        self::assertContains('root', $response['data']['roles']);
    }
}
