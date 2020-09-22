<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use App\LinkedSocialAccount;
use Illuminate\Validation\Rule;

class LoginRequest extends ApiFormRequest
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
        return [
            'type' => [
                'required',
                'string',
                Rule::in(['default', 'social'])
            ],
            'email' => 'required_if:type,default|string|email',
            'password' => 'required_if:type,default|string',
            'remember_me' => 'boolean',
            'provider' => [
                'required_if:type,social',
                'string',
                Rule::in(['github'])
            ],
            'token' => [
                'required_if:type,social',
                'exists:linked_social_accounts,token',
                static function ( $attribute, $value, $fail ) {
                    $exists = LinkedSocialAccount::where('token', $value)
                        ->where('provider_name', request()->provider)
                        ->exists();

                    if ( !$exists ) {
                        $fail($attribute . ' not exists');
                    }
                }
            ]
        ];
    }
}
