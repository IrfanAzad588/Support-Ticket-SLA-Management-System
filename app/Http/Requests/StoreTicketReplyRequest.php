<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],

            'is_internal' => ['nullable', 'boolean'],

            'attachments' => ['nullable', 'array', 'max:5'],

            'attachments.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,csv,txt',
                'max:5120',
            ],
        ];
    }
}
