<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Event::class) ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:events,slug'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_closed' => ['sometimes', 'boolean'],
            'artist_ids' => ['sometimes', 'array'],
            'artist_ids.*' => ['integer', 'distinct', 'exists:artists,id'],
            'banner' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        if ($this->user()->isAdmin()) {
            return $rules;
        }

        unset($rules['is_closed']);
        $rules['start_date'][] = 'after:now';
        $rules['artist_ids'] = ['required', 'array', 'min:1'];

        return $rules;
    }
}
