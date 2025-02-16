<?php

namespace App\Http\Requests\Transaction;

use Filament\Support\Assets\Js;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class Store extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->user()->role === 'customer';
    }


    public function rules(): array
    {
        return [
            'listing_id' => 'required|exists:listings,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'data' => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
