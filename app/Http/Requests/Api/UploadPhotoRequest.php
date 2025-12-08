<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadPhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User must be authenticated via sanctum middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048', // max 2MB
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
            'photo.required' => 'Foto profil harus diupload.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali file yang Anda upload.',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
