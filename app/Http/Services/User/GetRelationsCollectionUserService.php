<?php


namespace App\Http\Services\User;


use App\Http\Commands\Relation\GetCollectionRelationsCommand;
use App\Http\Resources\Relation\RelationCollectionResource;
use App\User;

class GetRelationsCollectionUserService
{
    private $user;
    private $status;
    private $page;
    private $per_page;
    private $order_column;
    private $order_direction;

    public function __construct ( User $user, Array $data )
    {
        $this->user = $user;
        $this->status = $data['status'] ?? null;
        $this->page = $data['page'] ?? 1;
        $this->per_page = $data['per_page'] ?? 20;
        $this->order_column = $data['order_column'] ?? 'id';
        $this->order_direction = $data['order_direction'] ?? 'asc';
    }

    /**
     * @return mixed
     */
    public function execute ()
    {
        $command = new GetCollectionRelationsCommand(
            [
                'user' => $this->user->id,
                'status' => $this->status
            ],
            ['applicant', 'addressee'],
            $this->page,
            $this->per_page,
            $this->order_column,
            $this->order_direction
        );
        return new RelationCollectionResource($command->execute());
    }
}
