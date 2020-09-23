<?php

namespace Tests\Feature\Services\User;

use App\Http\Resources\Relation\RelationCollectionResource;
use App\Http\Services\User\GetRelationsCollectionUserService;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetRelationsCollectionUserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $response, $user;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $users = User::limit($this->faker->numberBetween(1, 10))->get();
        $this->user = factory(User::class)->create();

        foreach ( $users as $addressee ) {
            factory(Relation::class)->create([
                'applicant_id' => $this->user->id,
                'addressee_id' => $addressee->id
            ]);
        }

        $this->response = (new GetRelationsCollectionUserService($this->user))->execute();
    }

    public function testGetCollection (): void
    {
        $this->withExceptionHandling();

        self::assertInstanceOf(RelationCollectionResource::class, $this->response);
        self::assertInstanceOf(Relation::class, $this->response->collection->first());
    }
}
