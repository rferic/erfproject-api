<?php


namespace App\Http\Commands\Abstracts;


abstract class GetCollectionAbstract
{
    protected $filters;
    protected $with;
    protected $page;
    protected $per_page;
    protected $order_column;
    protected $order_direction;
    protected $resource_class;

    public function __construct ( array $filters = [], array $with = [], int $page = 1, int $per_page = 20, string $order_column = null, string $order_direction = null )
    {
        $this->filters = $filters;
        $this->with = $with;
        $this->page = $page;
        $this->per_page = $per_page;
        $this->order_column = $order_column;
        $this->order_direction = $order_direction;
    }

    /**
     * @return array
     */
    public function getFilters (): array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getWith (): array
    {
        return $this->with;
    }

    /**
     * @return int
     */
    public function getPage (): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage (): int
    {
        return $this->per_page;
    }

    /**
     * @return string|null
     */
    public function getOrderColumn (): ?string
    {
        return $this->order_column;
    }

    /**
     * @return string|null
     */
    public function getOrderDirection (): ?string
    {
        return $this->order_direction;
    }

    public function executeRequest ( \Illuminate\Database\Eloquent\Builder $query )
    {
        $order_column = $this->getOrderColumn();
        $order_direction = $this->getOrderDirection();

        if ( isset($this->params['with']) ) {
            $query->with($this->params['with']);
        }

        if ( $order_column ) {
            $query->orderBy($order_column, $order_direction);
        }

        return $query->paginate($this->getPerPage(), ['*'], 'page', $this->getPage())->withQueryString();
    }

    public function execute ()
    {
        return $this->executeRequest($this->filter());
    }
}
