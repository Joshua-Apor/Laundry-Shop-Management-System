<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'customer_id' => ['nullable', 'integer', 'exists:customers,customer_id'],
            'address' => ['nullable', 'string', 'max:1000'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('services', 'service_id')->whereNull('deleted_at'),
            ],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'laundry_amount' => ['nullable', 'numeric', 'min:0'],
            'special_request' => ['nullable', 'string', 'max:1000'],
            'special_request_price' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:Cash,GCash'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['phone_number', 'customer_id'])) {
                return;
            }

            $selectedCustomerId = $this->input('customer_id');

            if ($selectedCustomerId !== null && $selectedCustomerId !== '') {
                $selectedCustomer = DB::table('customers')
                    ->where('customer_id', $selectedCustomerId)
                    ->value('contact_number');

                if ($selectedCustomer !== $this->input('phone_number')) {
                    $validator->errors()->add('phone_number', 'The selected customer does not match this phone number. Choose the customer again.');
                }

                return;
            }

            $existingCustomerId = DB::table('customers')
                ->where('contact_number', $this->input('phone_number'))
                ->value('customer_id');

            if ($existingCustomerId !== null) {
                $validator->errors()->add(
                    'phone_number',
                    'This phone number is already registered. Choose the existing customer or enter a different number.',
                );
            }
        }];
    }
}
