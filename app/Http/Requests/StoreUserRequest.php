<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            $rules = [
                'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'required',
            ];

            if ($this->roles === 'badanpublik') {
                $rules += [
                    'alamat' => 'nullable|string|max:250',
                    'telepon' => 'nullable|string|max:15',
                    'nama_responden' => 'nullable|string|max:250',
                    'jabatan_responden' => 'nullable|string|max:250',
                    'nohp_responden' => 'nullable|string|max:15',
                    'email_responden' => 'nullable|email|max:250',
                    'nama_ppid' => 'nullable|string|max:250',
                    'nohp_ppid' => 'nullable|string|max:15',
                    'email_ppid' => 'nullable|email|max:250',
                ];
            } else {
                $rules += [
                    'name' => 'required|string|max:250',
                ];
            }

            return $rules;
        }
}
