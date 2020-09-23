<?php

namespace Tests\Unit;

use App\Models\Relation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RelationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $applicant, $addressee, $relation;

    protected function setUp (): void
    {
        parent::setUp();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
        $this->relation = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'blocker_id' => null
        ]);
    }

    public function testHasApplicant (): void
    {
        self::assertInstanceOf(User::class, $this->relation->applicant);
        self::assertTrue($this->applicant->is($this->relation->applicant));
    }

    public function testHasAddressee (): void
    {
        self::assertInstanceOf(User::class, $this->relation->addressee);
        self::assertTrue($this->addressee->is($this->relation->addressee));
    }

    public function testNotHasBlocker (): void
    {
        self::assertNull($this->relation->blocker);
    }

    public function testHasBlocker (): void
    {
        $blocker = $this->faker->boolean ? $this->applicant : $this->addressee;
        $this->relation->blocker_id = $blocker->id;
        $this->relation->save();

        self::assertInstanceOf(User::class, $this->relation->blocker);
        self::assertTrue($blocker->is($this->relation->blocker));
    }
}
