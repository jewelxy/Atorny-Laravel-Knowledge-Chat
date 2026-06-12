<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;

class UploadCmsMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // max is in kilobytes (10240 KB = 10 MB)
            'image' => ['required', 'image', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.max' => 'The image must not be larger than 10 MB.',
        ];
    }
}
