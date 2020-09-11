<?php

namespace App\Http\Requests\User;


class EntityRequest extends WithRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $withRequest = new WithRequest();
        return [
            'with' => 'array',
            'with.*' => $withRequest->getWiths()
        ];
    }
}
