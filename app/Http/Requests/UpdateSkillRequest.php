<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $skill = $this->route('skill');
        
        return [
            'name' => 'required|string|max:255|unique:skills,name,' . $skill->id,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The skill name field is required.',
            'name.string' => 'The skill name must be a string.',
            'name.max' => 'The skill name may not be greater than 255 characters.',
            'name.unique' => 'This skill name is already taken.',
        ];
    }
}
