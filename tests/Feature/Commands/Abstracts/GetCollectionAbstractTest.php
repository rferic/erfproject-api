<?php

namespace Tests\Feature\Commands\Abstracts;

use App\Http\Commands\Abstracts\GetCollectionAbstract;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class GetCollectionAbstractTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $filters;
    protected $with;
    protected $page;
    protected $per_page;
    protected $order_column;
    protected $order_direction;
    protected $anonymousClassFromAbstract;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedUsers();

        $anonymousClassFromAbstract = new class extends GetCollectionAbstract {
            public function filter (): \Illuminate\Database\Eloquent\Builder
            {
                return User::query();
            }
        };
        $this->filters = [
            'is_verified' => $this->faker->randomElement([null, true, false]),
            'filter_text' => $this->faker->boolean ? 'a' : null,
            'role' => $this->faker->randomElement([null, 'client', 'root'])
        ];
        $this->with = $this->faker->shuffleArray(['roles', 'relations', 'applicantRelations', 'addresseeRelations', 'blockerRelations']);
        $this->page = $this->faker->numberBetween(1, 5);
        $this->per_page = $this->faker->numberBetween(1, 5);
        $this->order_column = $this->faker->randomElement(['id', 'email', 'name']);
        $this->order_direction = $this->faker->randomElement(['asc', 'desc']);

        $this->anonymousClassFromAbstract = new $anonymousClassFromAbstract(
            $this->filters,
            $this->with,
            $this->page,
            $this->per_page,
            $this->order_column,
            $this->order_direction
        );
    }

    public function testGetFilter (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getFilters();

        self::assertIsArray($response);
        self::assertEquals($this->filters, $response);
    }

    public function testGetWith (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getWith();

        self::assertIsArray($response);
        self::assertEquals($this->with, $response);
    }

    public function testGetPage (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getPage();

        self::assertIsNumeric($response);
        self::assertEquals($this->page, $response);
    }

    public function testGetPerPage (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getPerPage();

        self::assertIsNumeric($response);
        self::assertEquals($this->per_page, $response);
    }

    public function testGetOrderColumn (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getOrderColumn();

        self::assertIsString($response);
        self::assertEquals($this->order_column, $response);
    }

    public function testGetOrderDirection (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->getOrderDirection();

        self::assertIsString($response);
        self::assertEquals($this->order_direction, $response);
    }

    public function testExecute (): void
    {
        $this->withExceptionHandling();

        $response = $this->anonymousClassFromAbstract->execute();

        self::assertInstanceOf(LengthAwarePaginator::class, $response);
    }
}
