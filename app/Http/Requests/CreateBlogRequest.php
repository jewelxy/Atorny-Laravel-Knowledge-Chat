<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create blogs'); // Assuming permission
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|string|size:2|in:en,es',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.slug' => 'nullable|string|max:255|unique:blog_translations,slug',
            'translations.*.description' => 'required|string',
            'translations.*.meta_tag' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'translations.required' => __('At least one translation is required.'),
            'translations.*.locale.required' => __('Locale is required for each translation.'),
            'translations.*.title.required' => __('Title is required for each translation.'),
        ];
    }
}
