<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'provider';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date|before_or_equal:today',
            'value' => 'required|numeric|min:0|max:999999999.9999',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'dataset_id.required' => 'Veri seti seçimi zorunludur.',
            'dataset_id.exists' => 'Seçilen veri seti geçerli değil.',
            'date.required' => 'Tarih alanı zorunludur.',
            'date.date' => 'Geçerli bir tarih giriniz.',
            'date.before_or_equal' => 'Gelecek tarihli veri giremezsiniz.',
            'value.required' => 'Değer alanı zorunludur.',
            'value.numeric' => 'Değer sayısal olmalıdır.',
            'value.min' => 'Değer sıfırdan küçük olamaz.',
            'value.max' => 'Değer çok büyük.',
            'source_url.url' => 'Geçerli bir URL giriniz.',
            'source_url.max' => 'URL çok uzun.',
            'notes.max' => 'Notlar çok uzun.',
        ];
    }
}
