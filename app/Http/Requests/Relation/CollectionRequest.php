<?php

namespace App\Http\Requests\Relation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollectionRequest extends \App\Http\Requests\CollectionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->getCollectionRules([
            'user' => 'nullable|exists:user,id',
            'status' => [
                'nullable',
                'string',
                Rule::in(['pending', 'friendship', 'hate'])
            ]
        ]);
    }
}
