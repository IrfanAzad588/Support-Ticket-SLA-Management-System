<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && (auth()->user()->isCustomer() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],

            'description' => ['required', 'string', 'max:10000'],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'priority_id' => [
                'required',
                'exists:priorities,id',
            ],
        ];
    }
}
