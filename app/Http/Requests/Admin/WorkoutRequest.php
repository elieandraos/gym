<?php

namespace App\Http\Requests\Admin;

use App\Enums\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class WorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for an admin workout request.
     *
     * @return array An array of validation rules:
     *               - 'name': required string, maximum 255 characters.
     *               - 'categories': required array with at least one element.
     *               - 'categories.*': each element must be a valid value of the `Category` enum.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => [new Enum(Category::class)],
        ];
    }
}