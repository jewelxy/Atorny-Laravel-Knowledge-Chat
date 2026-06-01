<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsSectionTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supported = config('localization.supported_locales', ['en']);

        return [
            'locale' => ['required', 'string', 'max:5', Rule::in($supported)],
            // All translatable content — nested objects and arrays allowed.
            'data' => ['required', 'array'],
        ];
    }
}
