<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'string', 'in:income,expense'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'loan_id' => ['nullable', 'exists:loans,id'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurring_interval' => ['nullable', 'required_with:is_recurring', 'string', 'in:daily,weekly,monthly,yearly'],
        ];
    }
}
