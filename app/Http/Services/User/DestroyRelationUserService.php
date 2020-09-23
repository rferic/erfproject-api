<?php


namespace App\Http\Services\User;


use App\Http\Commands\Relation\DestroyRelationCommand;
use App\Models\User;

class DestroyRelationUserService
{
    private $command;

    public function __construct ( User $applicant, User $addressee )
    {
        $this->command = new DestroyRelationCommand($applicant, $addressee);
    }

    public function execute (): \App\Models\Relation
    {
        return $this->command->execute();
    }
}
