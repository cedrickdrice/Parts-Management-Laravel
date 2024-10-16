<?php

namespace App\Http\Requests\Parts;

use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UploadPartsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv', // Adjust max size as needed
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
