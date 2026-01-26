<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'statistician';
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
            'dataset_id' => 'required|exists:datasets,id',
            'rule_expression' => 'required|string|max:2000',
            'output_dataset_id' => 'nullable|exists:datasets,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kural adı zorunludur.',
            'name.max' => 'Kural adı çok uzun.',
            'description.max' => 'Açıklama çok uzun.',
            'dataset_id.required' => 'Veri seti seçimi zorunludur.',
            'dataset_id.exists' => 'Seçilen veri seti geçerli değil.',
            'rule_expression.required' => 'Kural ifadesi zorunludur.',
            'rule_expression.max' => 'Kural ifadesi çok uzun.',
            'output_dataset_id.exists' => 'Çıktı veri seti geçerli değil.',
        ];
    }
}
