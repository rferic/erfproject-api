<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\UpdateUserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $response, $user, $email, $name;

    protected function setUp (): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->email = $this->faker->safeEmail;
        $this->name = $this->faker->name;
        $this->response = (new UpdateUserService($this->user, [
            'email' => $this->email,
            'name' => $this->name
        ]))->execute();
    }

    public function testUpdated (): void
    {
        $this->withExceptionHandling();

        self::assertInstanceOf(User::class, $this->response);
        self::assertEquals($this->user->id, $this->response->id);
        self::assertEquals($this->user->email, $this->response->email);
        self::assertEquals($this->user->name, $this->response->name);
    }
}
