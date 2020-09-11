<?php


namespace App\Http\Services\User;


use App\Http\Commands\Relation\UpdateRelationCommand;
use App\User;

class UpdateRelationUserService
{
    private $command;

    public function __construct ( User $applicant, User $addressee, String $status )
    {
        $this->command = new UpdateRelationCommand($applicant, $addressee, $status);
    }

    public function execute (): \App\Relation
    {
        return $this->command->execute();
    }
}
