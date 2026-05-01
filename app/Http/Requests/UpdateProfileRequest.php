<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'website' => 'nullable|url',
            'nama_responden' => 'nullable|string|max:255',
            'jabatan_responden' => 'nullable|string|max:255',
            'nohp_responden' => 'nullable|string|max:50',
            'email_responden' => 'nullable|email',
            'nama_ppid' => 'nullable|string|max:255',
            'nohp_ppid' => 'nullable|string|max:50',
            'email_ppid' => 'nullable|email',
        ];
    }
}
