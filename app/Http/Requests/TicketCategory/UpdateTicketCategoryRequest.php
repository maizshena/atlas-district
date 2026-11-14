<?php

namespace App\Http\Requests\TicketCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTicketCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['sometimes', 'integer', 'exists:events,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'quota' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $category = $this->route('ticketCategory');
                $quota = $this->input('quota');

                if ($category !== null && $quota !== null && (int) $quota < $category->sold) {
                    $validator->errors()->add('quota', 'Kuota tidak boleh lebih kecil dari '.$category->sold.' tiket yang sudah terjual.');
                }
            },
        ];
    }
}
