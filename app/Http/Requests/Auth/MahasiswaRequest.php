<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MahasiswaRequest extends FormRequest
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
            'nim' => ['required', 'string','min:3'],
            'password' => ['required', 'string','min:3'],
        ];
    }
    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'nim.min' => 'NIM minimal :min karakter.',
            'password.min' => 'Password minimal :min karakter.',
        ];
    }
    public function logout()
    {
        Auth::logout();
        $this->session()->invalidate();
        $this->session()->regenerateToken();
    }

}
