<?php


namespace App\Http\Commands\Relation;


use App\Models\Relation;
use Illuminate\Validation\Rule;

class UpdateRelationCommand extends CreateRelationCommand
{
    public function execute (): Relation
    {
        $validator = $this->getValidator();

        if ( $validator->fails() ) {
            $validator->validate();
            exit();
        }

        $relation = $this->getQueryRelationExists($this->applicant, $this->addressee)->first();
        $relation->blocker_id = $this->status === 'hate' ? $this->applicant->id : null;
        $relation->status = $this->status;
        $relation->save();

        return $relation;
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

                    if ( !$this->getRelationExists($query) ) {
                        return $fail('This relation not exists');
                    }

                    $relation = $query->first();

                    if ( $relation->status === 'pending' && (int)$relation->addressee_id !== $this->applicant->id ) {
                        return $fail('The relationship is pending to be accepted by your friend');
                    }

                    if ( $this->status === 'hate' && $relation->status === $this->status ) {
                        return $fail('This relation already blocked');
                    }

                    if ( $this->status === 'friendship' && $relation->status === 'friendship' ) {
                        return $fail('This relation already accepted');
                    }

                    if ( $this->status === 'friendship' && $relation->status === 'hate' && (int)$relation->blocker->id !== $this->applicant->id ) {
                        return $fail('This relation has been blocked');
                    }
                }
            ],
            'status' => [
                'required',
                'string',
                Rule::in([
                    'friendship',
                    'hate'
                ])
            ]
        ];
    }
}
