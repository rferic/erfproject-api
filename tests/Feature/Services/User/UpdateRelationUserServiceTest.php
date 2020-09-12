<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\UpdateRelationUserService;
use App\Relation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateRelationUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $applicant, $addressee, $relation, $status;

    protected function setUp (): void
    {
        parent::setUp();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
        $this->relation = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);
        $this->status = $this->faker->randomElement(['friendship', 'hate']);

    }

    public function testAccept (): void
    {
        $this->withExceptionHandling();

        $status = 'friendship';

        $response = (new UpdateRelationUserService($this->addressee, $this->applicant, $status))->execute();

        self::assertInstanceOf(Relation::class, $response);
        self::assertEquals($this->relation->id, $response->id);
        self::assertEquals($this->applicant->id, $response->applicant_id);
        self::assertEquals($this->addressee->id, $response->addressee_id);
        self::assertEquals($status, $response->status);
        self::assertNull($response->blocker_id);
    }

    public function testBlock (): void
    {
        $this->withExceptionHandling();

        $status = 'hate';

        $response = (new UpdateRelationUserService($this->addressee, $this->applicant, $status))->execute();

        self::assertInstanceOf(Relation::class, $response);
        self::assertEquals($this->relation->id, $response->id);
        self::assertEquals($this->applicant->id, $response->applicant_id);
        self::assertEquals($this->addressee->id, $response->addressee_id);
        self::assertEquals($status, $response->status);
        self::assertEquals($this->addressee->id, $response->blocker_id);
    }
}
