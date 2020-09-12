<?php

namespace Tests\Feature\Commands\Relation;

use App\Http\Commands\Relation\CreateRelationCommand;
use App\Relation;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateRelationCommandTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $applicant;
    protected $addressee;
    protected $status;
    protected $command;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
        $this->status = $this->faker->randomElement(['pending', 'friendship', 'hate']);
        $this->command = new CreateRelationCommand($this->applicant, $this->addressee, $this->status);
    }

    public function testFailGetValidator (): void
    {
        $this->withExceptionHandling();

        $command = new CreateRelationCommand($this->applicant, $this->applicant, $this->faker->word);
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('addressee', $errors);
        self::assertArrayHasKey('status', $errors);
    }

    public function testSuccessGetValidator (): void
    {
        $this->withExceptionHandling();

        $validator = $this->command->getValidator();

        self::assertFalse($validator->fails());
    }

    public function testFailExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $this->expectException(ValidationException::class);

        $command = new CreateRelationCommand($this->applicant, $this->applicant, $this->faker->word);
        $command->execute();
    }

    public function testSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $response = $this->command->execute();
        $relation = Relation::where('applicant_id', $this->applicant->id)
            ->where('addressee_id', $this->addressee->id)
            ->where('status', $this->status)
            ->first();

        self::assertEquals($this->applicant->id, $response->applicant_id);
        self::assertEquals($this->addressee->id, $response->addressee_id);
        self::assertEquals($this->status, $response->status);
        self::assertTrue((bool)$relation);
        self::assertEquals($this->applicant->id, $relation->applicant_id);
        self::assertEquals($this->addressee->id, $relation->addressee_id);
        self::assertEquals($this->status, $relation->status);
    }

    public function testBlockerSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $command = new CreateRelationCommand($this->applicant, $this->addressee, 'hate');
        $response = $command->execute();
        $relation = Relation::where('applicant_id', $this->applicant->id)
            ->where('addressee_id', $this->addressee->id)
            ->where('status', 'hate')
            ->first();

        self::assertEquals($this->applicant->id, $response->blocker_id);
        self::assertEquals($this->applicant->id, $relation->blocker_id);
    }
}
