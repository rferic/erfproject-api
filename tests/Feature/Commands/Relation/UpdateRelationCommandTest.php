<?php

namespace Tests\Feature\Commands\Relation;

use App\Http\Commands\Relation\UpdateRelationCommand;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateRelationCommandTest extends TestCase
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

    public function testFailStatusGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        $command = new UpdateRelationCommand($this->applicant, $this->addressee, 'pending');
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('status', $errors);
    }

    public function testFailIsPendingGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);

        $command = new UpdateRelationCommand($this->applicant, $this->addressee, 'friendship');
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('addressee', $errors);
    }

    public function testFailAlreadyBlockedGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate'
        ]);

        $command = new UpdateRelationCommand($this->applicant, $this->addressee, 'hate');
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('addressee', $errors);
    }

    public function testFailAlreadyAcceptedGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'friendship'
        ]);

        $command = new UpdateRelationCommand($this->applicant, $this->addressee, 'friendship');
        $validator = $command->getValidator();
        $errors = $validator->errors()->toArray();

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('addressee', $errors);
    }

    public function testFailIsBlockedGetValidator (): void
    {
        $this->withExceptionHandling();

        factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        $command = new UpdateRelationCommand($this->applicant, $this->addressee, 'friendship');
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
            'status' => 'pending'
        ]);

        $command = new UpdateRelationCommand($this->addressee, $this->applicant, 'friendship');
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
            'status' => 'pending'
        ]);

        (new UpdateRelationCommand($this->applicant, $this->addressee, 'friendship'))->execute();
    }

    public function testAcceptSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $status = 'friendship';
        $relationCreated = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);

        $response = (new UpdateRelationCommand($this->addressee, $this->applicant, $status))->execute();
        $relation = Relation::find($relationCreated->id);

        $this->assertEntity($relation->toArray(), $response->toArray());
        self::assertEquals($status, $relation->status);
    }

    public function testBlockSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $status = 'hate';
        $relationCreated = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending'
        ]);

        $response = (new UpdateRelationCommand($this->addressee, $this->applicant, $status))->execute();
        $relation = Relation::find($relationCreated->id);

        $this->assertEntity($relation->toArray(), $response->toArray());
        self::assertEquals($status, $relation->status);
        self::assertEquals($this->addressee->id, $relation->blocker_id);
    }

    public function testUnBlockSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $status = 'friendship';
        $relationCreated = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'hate',
            'blocker_id' => $this->addressee->id
        ]);

        $response = (new UpdateRelationCommand($this->addressee, $this->applicant, $status))->execute();
        $relation = Relation::find($relationCreated->id);

        $this->assertEntity($relation->toArray(), $response->toArray());
        self::assertEquals($status, $relation->status);
        self::assertNull($relation->blocker_id);
    }
}
