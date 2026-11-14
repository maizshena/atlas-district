<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class ValidateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'ticket_code' => ['required', 'string', 'regex:/^[0-9]{4}-[0-9]{4}-[0-9]{4}$/'],
        ];
    }

    public function attributes(): array
    {
        return ['event_id' => 'event', 'ticket_code' => 'kode tiket'];
    }

    public function messages(): array
    {
        return ['ticket_code.regex' => 'Format kode tiket harus XXXX-XXXX-XXXX dengan angka 0-9.'];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('ticket_code'))) {
            $this->merge(['ticket_code' => trim($this->input('ticket_code'))]);
        }
    }
}
