<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cms_page_id' => ['required', 'integer', 'exists:cms_pages,id'],
            'section_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cms_sections', 'section_key')->where('cms_page_id', $this->input('cms_page_id')),
            ],
            'section_type' => ['required', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
