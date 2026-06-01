<?php

namespace App\Http\Requests\Cms;

use App\Models\CmsSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var CmsSection|null $section */
        $section = $this->route('section');

        return [
            'section_key' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('cms_sections', 'section_key')
                    ->where('cms_page_id', $section?->cms_page_id)
                    ->ignore($section?->id),
            ],
            'section_type' => ['sometimes', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
