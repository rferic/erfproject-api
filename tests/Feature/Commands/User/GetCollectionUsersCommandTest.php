<?php

namespace Tests\Feature\Commands\User;

use App\Http\Commands\User\GetCollectionUsersCommand;
use App\Http\Resources\User\UserCollectionResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GetCollectionUsersCommandTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

        $this->filters = $this->getFakeFilters();
        $this->with = $this->faker->shuffleArray(['roles', 'relations', 'applicantRelations', 'addresseeRelations', 'blockerRelations']);
        $this->page = $this->faker->numberBetween(1, 2);
        $this->per_page = $this->faker->numberBetween(1, 2);
        $this->order_column = $this->faker->randomElement(['id', 'email', 'name']);
        $this->order_direction = $this->faker->randomElement(['asc', 'desc']);

        $this->command = new GetCollectionUsersCommand(
            $this->filters,
            $this->with,
            $this->page,
            $this->per_page,
            $this->order_column,
            $this->order_direction
        );
        $this->response = new UserCollectionResource($this->command->execute());
    }

    public function testSuccessExecuteCommand (): void
    {
        $this->withExceptionHandling();

        $users = $this->command->filter()->get();

        self::assertEquals($this->page, $this->response->currentPage());
        self::assertEquals($this->per_page, $this->response->perPage());
        self::assertInstanceOf(Collection::class, $this->response->collection);
        self::assertEquals(count($users), $this->response->total());

        if ( $this->response->total() ) {
            self::assertInstanceOf(User::class, $this->response->collection->first());
        }
    }

    private function getFakeFilters (): array
    {
        $filters = [];

        if ( $this->faker->boolean ) {
            $filters['is_verified'] = true;
        }

        if ( $this->faker->boolean ) {
            $filters['filter_text'] = 'a';
        }

        if ( $this->faker->boolean ) {
            $filters['roles'] = 'client';
        }

        return $filters;
    }
}
