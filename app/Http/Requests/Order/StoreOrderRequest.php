<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.ticket_category_id' => ['required', 'integer', 'distinct', 'exists:ticket_categories,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
