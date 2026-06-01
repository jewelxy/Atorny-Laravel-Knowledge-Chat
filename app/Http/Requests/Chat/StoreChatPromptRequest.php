<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = config('localization.supported_locales', ['en']);

        $specialists = implode(',', config('chat.specialists') ? array_keys(config('chat.specialists')) : ['marco', 'ruby', 'alina']);

        return [
            'locale' => ['required', 'string', 'in:'.implode(',', $locales)],
            'specialist' => ['sometimes', 'nullable', 'string', 'in:'.$specialists],
            'question' => ['required', 'string', 'min:3', 'max:500'],
            'answer' => ['required', 'string', 'min:3', 'max:8000'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
