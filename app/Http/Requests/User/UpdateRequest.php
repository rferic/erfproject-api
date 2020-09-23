<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class UpdateRequest extends FormRequest
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
        $user = $this->user ?? auth()->user();

        return [
            'name' => 'string',
            'email' => [
                'string',
                'email',
                static function ( $attribute, $value, $fail ) use ( $user ) {
                    if ( User::where('email', $value)->where('id', '<>', $user)->count() ) {
                        $fail($attribute . ' already exists');
                    }
                }
            ],
            'password' => 'string|min:6'
        ];
    }
}
