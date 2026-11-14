<?php

namespace App\Http\Requests\TicketCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
        ];
    }

    // app/Http/Requests/TicketCategory/StoreTicketCategoryRequest.php
    // web nested route carries event id in the path
    protected function prepareForValidation(): void
    {
        if (! $this->filled('event_id') && $this->route('event')) {
            $this->merge(['event_id' => $this->route('event')->id]);
        }
    }
}
