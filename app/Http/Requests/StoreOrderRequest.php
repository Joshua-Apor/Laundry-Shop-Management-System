<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:11'],
            'address' => ['nullable', 'string', 'max:1000'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', 'string', 'distinct', 'in:Wash & Dry,Ironing,Folding,Self-Service'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'laundry_amount' => ['nullable', 'numeric', 'min:0'],
            'special_request' => ['nullable', 'string', 'max:1000'],
            'special_request_price' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:Cash,GCash'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
