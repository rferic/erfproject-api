<?php


namespace App\Http\Commands\Relation;


use App\Http\Commands\Interfaces\SaveModel;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateRelationCommand extends BaseRelationCommand implements SaveModel
{
    protected $applicant;
    protected $addressee;
    protected $status;

    public function __construct ( User $applicant, User $addressee, String $status )
    {
        $this->applicant = $applicant;
        $this->addressee = $addressee;
        $this->status = $status;
    }

    public function execute (): Relation
    {
        $validator = $this->getValidator();

        if ( $validator->fails() ) {
            $validator->validate();
            exit();
        }

        return Relation::create([
            'applicant_id' => $this->applicant->id,
            'addressee_id' => $this->addressee->id,
            'blocker_id' => $this->status === 'hate' ? $this->applicant->id : null,
            'status' => $this->status
        ]);
    }

    public function getValidator (): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make([
            'applicant' => $this->applicant->id,
            'addressee' => $this->addressee->id,
            'status' => $this->status
        ], $this->getRules());
    }

    public function getRules (): array
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

                    if ( $exists ) {
                        return $fail('This relation already exists');
                    }
                }
            ],
            'status' => [
                'required',
                'string',
                Rule::in([
                    'pending',
                    'friendship',
                    'hate'
                ])
            ]
        ];
    }
}
