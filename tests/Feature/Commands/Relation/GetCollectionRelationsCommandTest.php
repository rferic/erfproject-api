<?php

namespace Tests\Feature\Commands\Relation;

use App\Http\Commands\Relation\GetCollectionRelationsCommand;
use App\Http\Resources\Relation\RelationCollectionResource;
use App\Relation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GetCollectionRelationsCommandTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $relationsCount;
    protected $user;
    protected $filters;
    protected $with;
    protected $page;
    protected $per_page;
    protected $order_column;
    protected $order_direction;
    protected $command;
    protected $response;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $this->relationsCount = $this->faker->numberBetween(1, 30);
        $this->user = factory(User::class)->create();

        $this->with = $this->faker->shuffleArray(['applicant', 'addressee', 'blocker']);
        $this->page = $this->faker->numberBetween(1, 2);
        $this->per_page = $this->faker->numberBetween(1, 2);
        $this->order_column = $this->faker->randomElement(['id', 'status']);
        $this->order_direction = $this->faker->randomElement(['asc', 'desc']);
        $this->filters = $this->getFakeFilters();

        $users = User::limit($this->relationsCount)->where('email', '<>', $this->user->email)->get();

        foreach ( $users as $addressee ) {
            $blocker_id = $this->faker->boolean ? $this->user->id : null;

            factory(Relation::class)->create([
                'applicant_id' => $this->user->id,
                'addressee_id' => $addressee->id,
                'status' => $blocker_id ? 'hate' : $this->faker->randomElement(['pending', 'friendship']),
                'blocker_id' => $blocker_id
            ]);
        }
        $this->command = new GetCollectionRelationsCommand(
            $this->filters,
            $this->with,
            $this->page,
            $this->per_page,
            $this->order_column,
            $this->order_direction
        );
        $this->response = new RelationCollectionResource($this->command->execute());
    }

    public function testSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $relations = $this->command->filter()->get();

        self::assertEquals($this->page, $this->response->currentPage());
        self::assertEquals($this->per_page, $this->response->perPage());
        self::assertInstanceOf(Collection::class, $this->response->collection);
        self::assertEquals(count($relations), $this->response->total());

        if ( $this->response->total() > 0 ) {
            self::assertInstanceOf(Relation::class, $this->response->collection->first());
        }
    }

    private function getFakeFilters (): array
    {
        $filters = [];

        if ( $this->faker->boolean ) {
            $filters['user'] = $this->user->id;
        }

        if ( $this->faker->boolean ) {
            $filters['status'] = $this->faker->randomElement(['pending', 'friendship', 'hate']);
        }

        return $filters;
    }
}
