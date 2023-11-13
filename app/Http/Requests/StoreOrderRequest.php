<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'first' => 'required',
            'last' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required|numeric',
            'email' => 'required|email',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Електронска пошта е задолжително поле.',
            'email.email' => 'Електронска пошта мора да е валидна.',
            'phone.numeric' => 'Телфонскиот број мора да има само цифри.',
        ];
    }
}
