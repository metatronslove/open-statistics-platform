<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDatasetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $dataset = $this->route('dataset');
        
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'statistician') {
            return $dataset->created_by === $user->id;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Veri seti adı zorunludur.',
            'name.max' => 'Veri seti adı çok uzun.',
            'description.max' => 'Açıklama çok uzun.',
            'unit.required' => 'Birim alanı zorunludur.',
            'unit.max' => 'Birim çok uzun.',
            'calculation_rule.max' => 'Hesaplama kuralı çok uzun.',
        ];
    }
}
