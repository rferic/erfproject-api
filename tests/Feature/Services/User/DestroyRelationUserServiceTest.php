<?php

namespace Tests\Feature\Services\User;

use App\Http\Services\User\DestroyRelationUserService;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyRelationUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $relation, $response, $applicant, $addressee;

    protected function setUp (): void
    {
        parent::setUp();

        $this->applicant = factory(User::class)->create();
        $this->addressee = factory(User::class)->create();
        $this->relation = factory(Relation::class)->create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'status' => 'pending',
            'blocker_id' => null
        ]);
        $this->response = (new DestroyRelationUserService($this->applicant, $this->addressee))->execute();
    }

    public function testDestroyed (): void
    {
        $this->withExceptionHandling();

        $exists = Relation::where('applicant_id', $this->applicant->id)->where('addressee_id', $this->addressee->id)->exists();

        self::assertInstanceOf(Relation::class, $this->response);
        self::assertFalse($exists);
    }
}
