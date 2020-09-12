<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\RequestRelationUserService;
use App\Relation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestRelationUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $response, $applicant, $addressee, $status;

    protected function setUp (): void
    {
        parent::setUp();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
        $this->status = $this->faker->randomElement(['friendship', 'pending', 'hate']);
        $this->response = (new RequestRelationUserService($this->applicant, $this->addressee, $this->status))->execute();
    }

    public function testRequest (): void
    {
        $this->withExceptionHandling();

        self::assertInstanceOf(Relation::class, $this->response);
        self::assertEquals($this->applicant->id, $this->response->applicant_id);
        self::assertEquals($this->addressee->id, $this->response->addressee_id);
        self::assertEquals($this->status, $this->response->status);
    }
}
