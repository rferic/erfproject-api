<?php


namespace App\Http\Services\User;


use App\Http\Commands\Relation\UpdateRelationCommand;
use App\Models\Relation;
use App\Models\User;

class UpdateRelationUserService
{
    private $command;

    public function __construct ( User $applicant, User $addressee, String $status )
    {
        $this->command = new UpdateRelationCommand($applicant, $addressee, $status);
    }

    public function execute (): Relation
    {
        return $this->command->execute();
    }
}
