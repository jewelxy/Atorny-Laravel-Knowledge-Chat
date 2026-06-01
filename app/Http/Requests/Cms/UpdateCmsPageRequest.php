<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('page');

        return [
            'key' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('cms_pages', 'key')->ignore($pageId)],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
