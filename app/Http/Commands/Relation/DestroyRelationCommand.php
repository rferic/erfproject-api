<?php


namespace App\Http\Commands\Relation;


use App\Relation;
use App\User;
use Illuminate\Support\Facades\Validator;

class DestroyRelationCommand extends BaseRelationCommand
{
    protected $applicant;
    protected $addressee;

    public function __construct ( User $applicant, User $addressee )
    {
        $this->applicant = $applicant;
        $this->addressee = $addressee;
    }

    public function execute (): Relation
    {
        $validator = $this->getValidator();

        if ( $validator->fails() ) {
            $validator->validate();
            exit();
        }

        $relation = $this->getQueryRelationExists($this->applicant, $this->addressee)->first();
        $relation->delete();
        return $relation;
    }

    public function getValidator (): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make([
            'applicant' => $this->applicant->id,
            'addressee' => $this->addressee->id
        ], $this->getRules());
    }

    private function getRules (): array
    {
        return [
            'applicant' => 'required|exists:users,id',
            'addressee' => [
                'required',
                'different:applicant',
                'exists:users,id',
                function ( $attribute, $value, $fail ) {
                    $query = $this->getQueryRelationExists($this->applicant, $this->addressee);
                    $exists = $this->getRelationExists($query);

                    if ( !$exists ) {
                        return $fail('This relation not exists');
                    }

                    $relation = $query->first();

                    if ( $relation->status === 'hate' && $this->applicant->id !== $relation->blocker->id ) {
                        return $fail('You has been blocked');
                    }
                }
            ]
        ];
    }
}
