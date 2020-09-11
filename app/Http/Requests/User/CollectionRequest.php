<?php

namespace App\Http\Requests\User;


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
        $withRequest = new WithRequest();
        return $this->getCollectionRules([
            'is_verified' => 'boolean',
            'role' => 'string|exists:roles,name',
            'with' => 'array',
            'with.*' => $withRequest->getWiths()
        ]);
    }
}
