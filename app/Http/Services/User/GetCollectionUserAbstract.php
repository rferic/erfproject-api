<?php


namespace App\Http\Services\User;


use App\Http\Commands\User\GetCollectionUsers;

class GetCollectionUserAbstract
{
    private $filters;
    private $with;
    private $page;
    private $per_page;
    private $order_column;
    private $order_direction;

    public function __construct ( array $data )
    {
        $this->filters = $data['filters'] ?? [];
        $this->with = $data['with'] ?? [];
        $this->page = $data['page'] ?? 1;
        $this->per_page = $data['per_page'] ?? 20;
        $this->order_column = $data['order_column'] ?? 'id';
        $this->order_direction = $data['order_direction'] ?? 'asc';
    }

    public function execute ()
    {
        $command = new GetCollectionUsers(
            $this->filters,
            $this->with,
            $this->page,
            $this->per_page,
            $this->order_column,
            $this->order_direction
        );
        return $command->execute();
    }
}
