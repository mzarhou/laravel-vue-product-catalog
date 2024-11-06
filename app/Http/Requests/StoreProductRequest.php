<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['required', 'image', 'max:2048'], // 2MB max
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'exists:categories,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => 'Product image is required.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image must not be larger than 2MB.',
            'categories.required' => 'At least one category must be selected.',
            'categories.*.exists' => 'One or more selected categories are invalid.',
        ];
    }
}
