<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

// app/Http/Requests/Event/UpdateEventRequest.php ubah event penuh untuk admin dan hanya info dan banner untuk organizer
class UpdateEventRequest extends FormRequest
{
    private const ORGANIZER_FIELDS = ['name', 'description', 'banner'];

    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && ($this->user()?->can('updateInfo', $event) ?? false);
    }

    public function method(): string
    {
        return 'put';
    }

    public function rules(): array
    {
        $event = $this->route('event');
        $startDate = $this->input('start_date', $event?->start_date);

        $rules = [
            'venue_id' => ['sometimes', 'required', 'integer', 'exists:venues,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('events', 'slug')->ignore($event)],
            'description' => ['sometimes', 'required', 'string'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:'.$this->startBoundary($startDate)],
            'is_closed' => ['sometimes', 'boolean'],
            'artist_ids' => ['sometimes', 'array'],
            'artist_ids.*' => ['integer', 'distinct', 'exists:artists,id'],
            'banner' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        return $this->user()->isAdmin() ? $rules : Arr::only($rules, self::ORGANIZER_FIELDS);
    }

    private function startBoundary(mixed $startDate): string
    {
        return $startDate instanceof \DateTimeInterface ? $startDate->format('Y-m-d H:i:s') : (string) $startDate;
    }
}