<?php

namespace Tests\Feature\Commands\Relation;

use App\Http\Commands\Relation\DestroyRelationCommand;
use App\Relation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyRelationCommandTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $applicant;
    protected $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
    }

    public function testFailGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        $command = new DestroyRelationCommand($this->applicant, $this->addressee);
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('addressee', $errors);
    }

    public function testSuccessGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        $command = new DestroyRelationCommand($this->addressee, $this->applicant);
        $validator = $command->getValidator();

        self::assertFalse($validator->fails());
    }

    public function testFailExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $this->expectException(ValidationException::class);

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        (new DestroyRelationCommand($this->applicant, $this->addressee))->execute();
    }

    public function testSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $relation = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending',
            'blocker_id' => null
        ]);

        $response = (new DestroyRelationCommand($this->applicant, $this->addressee))->execute();
        $exists = Relation::where('id', $relation->id)->exists();

        $this->assertEntity($relation->toArray(), $response->toArray());
        self::assertFalse($exists);
    }
}
