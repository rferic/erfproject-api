<?php


namespace App\Http\Requests\User;


use App\Http\Requests\EntityWithInterface;
use Illuminate\Validation\Rule;

class WithRequest implements EntityWithInterface
{
    public function getWiths (): array
    {
        return [
            'string',
            Rule::in(['applicant_relations', 'addressee_relations', 'relations'])
        ];
    }
}
