<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && ($this->user()?->can('manageNotices', $event) ?? false);
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:160', 'not_regex:/(https?:\/\/|www\.)/i'],
            'days' => ['required', 'integer', 'in:1,3,7,14'],
        ];
    }

    public function attributes(): array
    {
        return ['message' => 'pengumuman', 'days' => 'lama tampil'];
    }

    public function messages(): array
    {
        return ['message.not_regex' => 'Pengumuman tidak boleh berisi tautan.'];
    }
}
