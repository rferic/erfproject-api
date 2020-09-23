<?php


namespace App\Http\Services\User;


use App\Http\Commands\Relation\CreateRelationCommand;
use App\Models\Relation;
use App\Models\User;

class RequestRelationUserService
{
    private $command;

    public function __construct ( User $applicant, User $addressee, String $relationStatus = 'pending' )
    {
        $this->command = new CreateRelationCommand($applicant, $addressee, $relationStatus);
    }

    public function execute (): Relation
    {
        return $this->command->execute();
    }
}
