<?php

namespace App\Http\Requests;

use App\Http\Utils\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

abstract class ApiFormRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return HttpResponseException
     *
     * @throws ValidationException
     */
    protected function failedValidation ( Validator $validator ): HttpResponseException
    {
        if ( request()->expectsJson() ) {
            $status = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

            throw new HttpResponseException(
                $this->responseFail($validator->errors()->messages(), JsonResponse::$statusTexts[$status], $status)
            );
        }

        parent::failedValidation($validator);
    }
}
