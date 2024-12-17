<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TriggerLogicAppRequest extends FormRequest
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
        return [
            'file_name' => 'required|string|max:255',
            'file_content' => 'required|file|mimes:pdf|max:10240', // Ensure it's a PDF under 10MB
        ];
    }
}
