<?php

namespace Tests\Feature\Services\User;

use App\Http\Resources\User\UserCollectionResource;
use App\Http\Services\User\GetCollectionUserService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetCollectionUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $response;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->response = (new GetCollectionUserService())->execute();
    }

    public function testGetCollection (): void
    {
        $this->withExceptionHandling();

        self::assertInstanceOf(UserCollectionResource::class, $this->response);
        self::assertInstanceOf(User::class, $this->response->collection->first());
    }
}
