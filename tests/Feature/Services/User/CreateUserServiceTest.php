<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\CreateUserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $data, $response;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->data = [
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
            'password' => $this->faker->password
        ];
        $this->response = (new CreateUserService($this->data))->execute();
    }

    public function testCreated (): void
    {
        $this->withExceptionHandling();

        self::assertInstanceOf(User::class, $this->response);
        self::assertEquals($this->data['email'], $this->response->email);
        self::assertEquals($this->data['name'], $this->response->name);
    }
}
