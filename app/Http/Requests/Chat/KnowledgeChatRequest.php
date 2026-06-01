<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = config('localization.supported_locales', ['en']);
        $specialists = implode(',', array_keys(config('chat.specialists', ['marco' => [], 'ruby' => [], 'alina' => []])));

        return [
            'message' => ['required', 'string', 'min:2', 'max:2000'],
            'locale' => ['sometimes', 'string', 'in:'.implode(',', $locales)],
            'specialist' => ['sometimes', 'string', 'in:'.$specialists],
            'history' => ['sometimes', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:4000'],
        ];
    }
}
