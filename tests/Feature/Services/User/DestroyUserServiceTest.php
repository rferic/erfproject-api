<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\DestroyUserService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user, $response;

    protected function setUp (): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->response = (new DestroyUserService($this->user))->execute();
    }

    public function testDestroyed (): void
    {
        $this->withExceptionHandling();

        $exists = User::where('id', $this->user->id)->exists();

        self::assertInstanceOf(User::class, $this->response);
        self::assertFalse($exists);
    }
}
