<?php

namespace Tests\Unit;


use App\Models\Relation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $me, $roles, $applicantRelationsUsers, $addresseeRelationsUsers;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        $this->me = factory(User::class)->create();
        $this->roles = Role::all();

        $this->applicantRelationsUsers = $this->generateUserRelations(true);
        $this->addresseeRelationsUsers = $this->generateUserRelations(false);

        foreach ( $this->roles as $role ) {
            $this->me->attachRole($role);
        }
    }

    public function testHasRoles (): void
    {
        self::assertCount(count($this->roles), $this->me->roles);
        self::assertInstanceOf(Collection::class, $this->me->roles);
        self::assertInstanceOf(Role::class, $this->me->roles->first());
    }

    public function testHasRelations (): void
    {
        $count = count($this->applicantRelationsUsers) + count($this->addresseeRelationsUsers);
        self::assertCount($count, $this->me->relations());
        self::assertInstanceOf(Collection::class, $this->me->relations());
        self::assertInstanceOf(Relation::class, $this->me->relations()->first());
    }

    public function testHasApplicantRelations (): void
    {
        self::assertCount(count($this->applicantRelationsUsers), $this->me->applicantRelations);
        self::assertInstanceOf(Collection::class, $this->me->applicantRelations);
        self::assertInstanceOf(Relation::class, $this->me->applicantRelations->first());
    }

    public function testHasAddresseeRelations (): void
    {
        self::assertCount(count($this->addresseeRelationsUsers), $this->me->addresseeRelations);
        self::assertInstanceOf(Collection::class, $this->me->addresseeRelations);
        self::assertInstanceOf(Relation::class, $this->me->addresseeRelations->first());
    }

    public function testHasBlockerRelations (): void
    {
        $this->me->addresseeRelations()->update([
            'status' => 'hate',
            'blocker_id' => $this->me->id
        ]);
        self::assertCount(count($this->addresseeRelationsUsers), $this->me->blockerRelations);
        self::assertInstanceOf(Collection::class, $this->me->blockerRelations);
        self::assertInstanceOf(Relation::class, $this->me->blockerRelations->first());
    }

    public function testHasIsVerified (): void
    {
        self::assertIsBool($this->me->is_verified);
    }

    private function generateUserRelations ( Bool $isApplicant )
    {
        $users = factory(User::class, $this->faker->numberBetween(1, 10))->create();

        foreach ( $users as $user ) {
            factory(Relation::class)->create([
                'applicant_id' => $isApplicant ? $this->me->id : $user->id,
                'addressee_id' => $isApplicant ? $user->id : $this->me->id,
                'status' => 'pending',
                'blocker_id' => null
            ]);
        }

        return $users;
    }
}
