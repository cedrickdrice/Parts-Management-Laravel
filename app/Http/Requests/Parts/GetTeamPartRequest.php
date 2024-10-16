<?php

namespace App\Http\Requests\Parts;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class GetTeamPartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change this if you have authorization logic
    }

    public function rules()
    {
        return [
            'page'      => 'integer|min:1',
            'per_page'  => 'integer|min:1',
        ];
    }

    /**
     * Execute when validation fails
     * @param Validator $oValidator
     */
    protected function failedValidation(Validator $oValidator)
    {
        $response = Response::json([
            'result'  => 'failed',
            'message' => [
                'message'   => 'The given data is invalid',
                'error'     => $oValidator->errors()
            ],
        ], 404);

        throw new ValidationException($oValidator, $response);
    }
}
